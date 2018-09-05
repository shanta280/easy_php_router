<?php 
namespace Shantanu;

class EasyRouter {
    private $routes = [];
    private $paths = [];
    private $uri = [];
    private $requestMethod = "GET"; // default
    private $page404 = null;

    public function __construct() {        
        // setting data here
        // will return 
        // [ path => [], query => []]
        $this->uri = parse_url($_SERVER['REQUEST_URI']);

        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
    }
    
   
   // GET routes
   public function get($path, $callback) {
        $this->route('GET', $path, $callback);
        return $this;
    }
    // POST route
    public function post($path, $callback) {
        $this->route('POST', $path, $callback);
        return $this;
    }

    // PUT route
    public function put($path, $callback) {
        $this->route('PUT', $path, $callback);
        
        return $this;
    }

    // DELETE route
    public function delete($path, $callback) {
        $this->route('DELETE', $path, $callback);
        
        return $this;
    }

    // this function will support any method
    // get, post, put, etc
    public function any($path, $callback) {
        $this->route('ANY', $path, $callback);
        return $this;
    }
    // this is the main function
    // it sets the routes for comparision later on

   public function route($method, $path, $callback) {
        $obj = new \stdClass();
        $obj->callback = $callback;
        $obj->method = $method;
        
        $this->routes[$path] = $obj;
        
        return $this;
    } 

    public function set404($callback) {
        $this->page404 = $callback;
    }
   // #####################################################   
   // This function starts the routing process
   // 
   public function run() {
        // extracts the path from uri
        $this->paths = explode("/", urldecode(trim($this->uri['path'], '/')));
        
        // if the size of the path 
        // is == 1 ([index].php) it means we have to load to home page
        if(sizeof($this->paths) == 1) {
            // / home page
            $this->loadPath('/');

        } else {
            // remove filename.php from uri
            array_splice($this->paths, 0, 1);
            
            $this->parseRoutes();// 
        }
   }
   
   private function parseRoutes() {
        $path = [];
        $args = [];
        
        // Routes That we need to check for
        // wrote outsite loop
        $eligibleRoutes = array_filter(array_keys($this->routes), function( $key) {
            $tmp = explode("/", trim($key, "/"));
            return sizeof($tmp) == sizeof($this->paths);
        });//
        
                
        for($i = 0; $i<sizeof($this->paths); $i++) {
        
            $param = $this->checkDynamicParams(
                $i, // index
                $this->paths[$i], // paths
                "/".implode("/", $path), // parsed uri
                $eligibleRoutes// routes
            );
           // echo '*PARAM = '.$param.'<hr>';
            if(sizeof($path) == 0) {
                $tmp = "/".$param;
            } else {
                $tmp = "/".implode("/", $path)."/".$param;
            }
            
            $path[] = $param;
            if($param !== $this->paths[$i]) {
                // store as key value array
                // removes { and } though not necessary 
                $args[trim(trim($param, "{"), "}")] = $this->paths[$i];
            }
        }
        
        // function path is not available
        if(sizeof($path) == 0) { 
            // page not found 
            $path = "/".implode("/", $this->paths);
        } else {
            $path = "/".implode("/", $path);
        }
        
        // loads the page
        // args is the parameters to be passed to the 
        // target function 
        $this->loadPath($path, $args);
   }
   
   
   private function checkDynamicParams($index, $param, $uri, $routes) {
        // NOTE THERE SHOULD A BETTER WAY
        // BUT FOR NOW GOING WITH THIS
        
        // return the same param if it matches
        // the route {?}
        foreach($routes as $key) {
            $keys = explode('/', trim($key, '/'));
            if($keys[$index] == $param) {
            
                return $param; 
            } 
        }
        
        // Second loop becuase it returns
        // the same {?} again 
        foreach($routes as $key) {
            $keys = explode('/', trim($key, '/'));
            // have to put regex here later
            if($this->startsWith($keys[$index], "{") && $this->endsWith($keys[$index], "}")) {
                return $keys[$index];
            }
        }
        return $param;
   }
   
   // check if a string startes with specified characters
   private function startsWith($str, $key, $length = 1) {
   
        return substr($str, 0, $length) === $key;
   }
   
   // checks if a string ends with ? characters
   private function endsWith($str, $key, $length = 1) {
   
        return substr($str, strlen($str)-1, $length) === $key;
   }
   
   // Calls the actual function 
   private function loadPath($path, $params = []) {
        // checks if the route exists in our routes
        if(array_key_exists($path, $this->routes)) {
            
            // the route
            $route = $this->routes[$path];

            // check the request method
            if($route->method !== $this->requestMethod) {
                if($route->method !=='ANY') {
                    // method not allowed
                    http_response_code(405);
                    die('METHOD Not Allowed');
                }
            }
            

            switch(gettype($route->callback)) {
                
                case 'object':
                    // Closure   
                    $this->callFunction($route->callback, $params);
                    break;
                case 'string':
                    // namespace (probably)
                    $this->callFunction($route->callback, $params);
                    break;
                case 'array': 
                    // class like
                    // [classname, method]
                    $this->callFunction([new $route->callback[0](), $route->callback[1]], $params);
                    break;
                default:
                    die("Invalid Argument Specified for: ".$path);
            }
            
        } else {
            $this->show404();
        }
        
   }

    // Calls the actual function 
    private function callFunction($method, $params) {
    // do someting more 
        // 
        call_user_func_array($method, $params);
        
        // do something more here
    }

    public function show404() {
        http_response_code(404);
        if($this->page404) {
            call_user_func($this->page404);
        } else {
            die('PAGE NOT FOUND');
        }
    }

}