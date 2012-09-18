<?php


	function SNAPTrigger($module){
		
		$action = $_GET["mode"];
		switch($action){
			case "js":
				$module->exportJS();
				break;
			case "execute":
				header("Content-Type: text/javascript");
				$response = array();

				if(!isset($_POST["function"])){
					$response["error"] = "Key Error: No function name sent.";
				}
				else{
					$args = array(); 

					$function = $_POST["function"];
					if(isset($_POST["args"])){
						$args = $_POST["args"];
					}

					
					foreach($args as $key=>$val){
						$args[$key] = json_decode($val);
					}


					try{
						$result = $module->execute($_POST["function"], $args);
						$response["data"] = $result;
					}catch(Exception $e){
						$response["error"] = $e->getMessage(); // $e;
						
					}




				}


				echo json_encode((object)$response);

				break;
		}
	}
	

	class SNAPModule{

		private $jsname;
		private $functions;


		public function __construct($javascriptName = "MODULE"){

			$this->jsname = $javascriptName;
			$this->functions = array();


		}


		public function registerFunction($registerName, $functionName, $filePath){
			$this->functions[] = array("registerName"=>$registerName, "filePath"=>$filePath, "functionName"=>$functionName);
		}

		public function exportJS(){
			
			header("Content-Type: text/javascript");

			echo "var {$this->jsname} = {};\n";

			foreach($this->functions as $func){
				

				echo "{$this->jsname}.{$func["registerName"]} = function(){\n";
				echo "	return {$this->jsname}.call(\"{$func["registerName"]}\", arguments);";
				echo "};";
				
				
			}


			
			echo "{$this->jsname}.call = function(methodName, args){\n";
			
			// initialize the variables -- xhr, requestURI and queryString
			echo "	var xhr = new XMLHttpRequest(); \n";
			echo "	var requestURI = '{$_SERVER["REQUEST_URI"]}';\n";
			echo "	var queryString = 'function=' + methodName;\n";
			
			echo "	for(var i = 0; i < args.length; i++){\n";
			echo "		queryString += '&args[]=' + encodeURIComponent(JSON.stringify(args[i]));\n"; 
			echo "	};\n"; 
			

			// send the query string.
			echo "	requestURI = requestURI.replace(/\?.*/, '') + '?mode=execute';\n";
			echo "	xhr.open('post', requestURI, false);\n";
			echo "	xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');\n";
			echo "	xhr.send(queryString);\n"; 
			echo "	console.log('sending query string: ' + queryString);\n"; 
			

			// parse the resopnse.
			echo "	console.log(xhr.responseText);\n";
			echo "	var response = JSON.parse(xhr.responseText);\n";
			echo "	console.log(response);\n";
			echo "	if(typeof response.error == 'string'){throw new Error( 'PHP error: ' + response.error)}\n";
			echo "	else{return response.data};\n";
			echo "};"; // end function call()


			//echo "MODULE.foo('Lee');";
			
		}


		public function execute($functionName, $args){
 
			// errors will still generate nice JSON because now they are treated as exceptions.
			function errHandle($errno, $errstr, $errfile, $errline){
				switch($errno){
					case E_ERROR:
						$errtype = "Error";
						break;
					case E_WARNING:
						$errtype = "Warning";
						break;
					case E_NOTICE:
						$errtype = "Notice";
						break;
					default:
						$errtype = "Unknown Error Type";
				}

				throw new Exception( "$errtype  : $errstr   in  $errfile  on  line $errline" );
			}


			set_error_handler(errHandle);

			$func = NULL;
			foreach($this->functions as $afunction){
				if($afunction["registerName"] == $functionName){
					$func = $afunction;
					break;
				}
			}

			if($func == NULL){
				throw new Exception("That function does not exist");
			}


			
			if(file_exists($func["filePath"])){
				require_once $func["filePath"];
			}

			return call_user_func_array($func["functionName"], $args);


		}

	}


	

	
	/* Usage
	$module = new SNAPModule();

	$module->registerFunction("foo",  "foo", "bar.php");
	$module->registerFunction("sTime", "getServerTime", "bar.php");




	SNAPTrigger($module);

	*/
	
?>