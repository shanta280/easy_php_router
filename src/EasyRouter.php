<?php 
namespace Shantanu\EasyRouter;

class EasyRouter {
    
    protected $routes = [];
    protected $paths = [];
    protected $uri = [];
    protected $requestMethod = "GET"; // default
    
    public function __construct() {        
        // setup
        // 
        // [ path => [], query => []]
        $this->uri = parse_url($_SERVER['REQUEST_URI']);

        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        
    }
    
    public static function start() {
        // To Enable Chaining 
        // example
        // Router::start()->get('/', 'example')->run();
        // though useless in this case
        return new EasyRouter();
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
    
   
   // sets the route
   public function route($method, $path, $callback) {
        $obj = new \stdClass();
        $obj->callback = $callback;
        $obj->method = $method;
        
        $this->routes[$path] = $obj;
        
        return $this;
   } 
   
   public function show404() {
        // * 
        if(array_key_exists('*', $this->routes)) {
            $this->loadPath("*", []);
        } else {
            http_response_code(404);
            die("404! PAGE NOT FOUND");
        }
   }
    
    
    // #####################################################   
   // This function starts the routing process
   //
   public function run() {
   
        
        $this->paths = explode("/", urldecode(trim($this->uri['path'], '/')));
        
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
   
   // checkes the request method etc
   private function loadPath($path, $params = []) {
        // checks if the route exists in our routes

        if(array_key_exists($path, $this->routes)) {
            // the route
            $route = $this->routes[$path];
            
            // check the request method
            if($route->method !== $this->requestMethod) {
                // method not allowed
                http_response_code(405);
                die('METHOD Not Allowed');
            }
            
            //echo gettype($route->callback).'<br>';
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
                    $this->show404();
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
}
