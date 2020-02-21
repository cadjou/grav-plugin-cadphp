<?php
/**
 * @package    Grav\Plugin\CadPHP
 *
 * @copyright  Copyright (C) 2018 - 2019 CaDJoU <cadjou@gmail.com>.
 * @license    MIT License; see LICENSE file for details.
 */
namespace Grav\Plugin\Cadphp;

use Grav\Common\Grav;

class Cadphp
{
    /** @var Config_plugin */
    protected $configPlugin;

    /** @var docRoot */
    protected $docRoot;

    /** @var pathPlugin */
    protected $pathPlugin;

    /** @var gravLog */
    protected $gravLog;

    /** @var gravdebug */
    protected $gravDebug;

    /** @var grav */
    protected $grav;
	
    /** @var init */
    protected $init;
	
    /** @var cadVaraibles */
    protected $cadVaraibles;

    /** @var keyWord */
    protected $keyword = 'cadphp';

    public function __construct(Grav $grav, Array $config)
    {
		$this->grav          = $grav;       // Add the PHP Error in case
		$this->gravLog       = $grav['log'];       // Add the PHP Error in case
		$this->gravDebug     = $grav['debugger'];
		
		$this->configPlugin  = $config;            // Get the Configuration Plugin in cadphp/cadphp.yaml and cadphp/blueprints.yaml

		$this->docRoot           = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';
		$this->pathPlugin        = __DIR__ . '/..';
    }
	
	public function headerArray(Array $header)
	{
		$return = [];
		foreach($header as $key=>$values)
		{
			$return[$key] = $this->headerString($values);
		}
		return $return;
	}
	
	public function headerString(String $header)
	{
		$header   = '
' .     $this->keyword . ':' . $header;
		$result = $this->regexCadTag($header);
		return $this->calculPath(current($result));
	}
	
	public function regexCadTag($content)
	{
		// Regex to to find the Keyword by default is cadphp
		$regex   = '/^' . $this->keyword . ':(p\d{1,2}):(\S*)/m';
		preg_match_all($regex, $content, $results, PREG_SET_ORDER, 0);
		return $results;
	}
	public function regexCadVar($content)
	{
		// Regex to to find the Keyword by default is cadphp
		$regex   = '/\W_' . $this->keyword . '\.(\w+)/m';
		preg_match_all($regex, $content, $results, PREG_SET_ORDER, 0);
		return $results;
	}
	
	public function calculPath(Array $cadFormat)
	{
		$config  = $this->configPlugin;
		if (!empty($cadFormat[1]) and !empty($config[$cadFormat[1]]) and !empty($cadFormat[2]))
		{
			// Get the Predefined Path from keyword PXX (00 - 99)
			$p     = rtrim($config[$cadFormat[1]],'/');
			// Get the racin path in depend of the beginning of the Predefined Path (Absolu or Relative Path)
			$racin = substr($p,0,1) == '/' ? $this->docRoot : $this->pathPlugin;
			// Return Array ID => Path
			return $racin . '/' . $p . '/'. $cadFormat[2] . '.php';
		}
		if (!empty($cadFormat[2]))
		{
			$this->gravdebug->addMessage('The P number ' . $cadFormat[1] . ' is not good');
			$this->gravLog->warning('The P number ' . $cadFormat[1] . ' is not good');
		}
		else
		{
			$this->gravdebug->addMessage('There is no end ' . $cadFormat[0]);
			$this->gravLog->warning('There is no end ' . $cadFormat[0]);
		}
		return null;
	}
	
	public function process(String $content)
	{		
		return $this->processContent($content);
	}
	
	public function processContent(String $content, $cadPHPHeader = null)
	{
		if ($cadPHPHeader)
		{
			$cadPHPHeader = is_string($cadPHPHeader) ? [$cadPHPHeader=>$cadPHPHeader] : $cadPHPHeader;
			foreach($this->headerArray($cadPHPHeader) as $key=>$path)
			{
				$this->cadVaraibles[$key] = $this->safeEval($path)  ? include($path) : '';
			}
		}
		
		$tabInclude = $this->calculPhp($content);

		foreach($tabInclude as $identifiant => $retour_php)
		{
			$content		= str_replace($identifiant,$retour_php,$content);
		}
		return $content;
	}
	
	public function processForm(Array $params, Array $data)
	{
		$config	= $this->configPlugin;
		$retour = [];
		foreach($data as $key => $value)
		{
			$tmp = 'C_' . $key;
			$$tmp = $value;
		}
		foreach($params as $var => $id)
		{
			$regex	 = '/^(p\d{1,2}):(\S*)/m';
			preg_match_all($regex, $id, $results, PREG_SET_ORDER, 0);
			if (!empty($results[0])
			and !empty($results[0][0])
			and !empty($results[0][1]) 
			and !empty($results[0][2]))
			{
				$p			 = rtrim($config[$results[0][1]],'/');
				$identifiant = $results[0][0];
				$racin		 = substr($p,0,1) == '/' ? $this->docRoot : $this->pathPlugin;
				$chemin		 = $racin . '/' . $p . '/'. $results[0][2] . '.php';
				$retour[$var] = include($chemin);
			}
		}
		return $retour;
	}
	
	public function processData($data)
	{
		print_r('coucou');
		$config	= $this->configPlugin;
		$retour = null;
		$data = is_array($data) ? $data : [$data];
		foreach($data as $id)
		{
			$regex	 = '/^(p\d{1,2}):(\S*)/m';
			preg_match_all($regex, $id, $results, PREG_SET_ORDER, 0);
			if (!empty($results[0])
			and !empty($results[0][0])
			and !empty($results[0][1]) 
			and !empty($results[0][2]))
			{
				print_r($results);
				$p			 = rtrim($config[$results[0][1]],'/');
				$identifiant = $results[0][0];
				$racin		 = substr($p,0,1) == '/' ? $this->docRoot : $this->pathPlugin;
				$chemin		 = $racin . '/' . $p . '/'. $results[0][2] . '.php';
				$retour		 = include($chemin);
			}
		}
		return $retour;
	}
	
	public function calculPhp(String &$content)
	{
		$config	 = $this->configPlugin;
		$results = $this->regexCadTag($content);
		
		$return = [];
		foreach($results as $result)
		{
			if (!empty($result[0]) and isset($result[2]))
			{
				$identifiant  = $result[0];
				$uniqueID	  = uniqid('cadjouphp_');
				$replace	  = (!empty($result[1]) and !empty($config[$result[1]])) ? $uniqueID  : false;
				
				$tabContent   = explode($identifiant,$content);
				$contentFirst = $tabContent[0];
				unset($tabContent[0]);
				$contentLasts = $tabContent ? implode($identifiant,$tabContent) : '';
				$content = $contentFirst . $replace . $contentLasts;
				
				if ($replace)
				{
					$p					= rtrim($config[$result[1]],'/');
					$racin				= substr($p,0,1) == '/' ? $this->docRoot : $this->pathPlugin;
					$path				= $racin . '/' . $p . '/'. $result[2] . '.php';
					$return[$replace]	= $this->safeEval($path)  ? include($path) : '';
				}
			}
		}
		
		$results = $this->regexCadVar($content);
		foreach($results as $result)
		{
			if (!empty($result[0]))
			{
				$identifiant  = $result[0];
				$uniqueID	  = uniqid('cadjouphp_');
				$replace	  = !empty($result[1]) ? $uniqueID  : false;
				
				$tabContent   = explode($identifiant,$content);
				$contentFirst = $tabContent[0];
				unset($tabContent[0]);
				$contentLasts = $tabContent ? implode($identifiant,$tabContent) : '';
				$content = $contentFirst . $replace . $contentLasts;
			}
			if ($replace)
			{
				$content = $contentFirst . $replace . $contentLasts;
				$return[$replace] = $this->cadVaraibles[$result[1]]; 
			}
		}
		return $return;
	}
	
	public function safeEval($path)
	{
		if (!is_file($path))
		{
			$this->gravDebug->addMessage('The path ' . $path . ' is not a file');
			$this->gravLog->warning('The path ' . $path . ' is not a file');
			return false;
		}
		$config  = $this->configPlugin;

		// Get the PHP and Remove the PHP Balises
		$code    = php_strip_whitespace($path);
		$code    = str_replace('<?php','',$code);
		$code    = str_replace('?>'   ,'',$code);

		// Get the deny functions, by default it's allow_url_fopen / allow_url_include / exec / shell_exec / system / passthru / popen / stream_select / ini_set
		$tab_function_deny = isset($config['functions_deny']) ? $config['functions_deny'] : [];
		$tab_function_deny = (is_array($tab_function_deny))   ? $tab_function_deny        : [];

		// Check for each function if it's exist or not
		foreach($tab_function_deny as $functions)
		{
			$regex = '/\s' . $functions . '\s*\(/m';
			preg_match_all($regex, $code, $findDenyFunction, PREG_SET_ORDER, 0);
			if ($findDenyFunction)
			{
				$this->gravDebug->addMessage('The PHP code had a erreur : ' . $functions . ' is detected');
				$this->gravLog->warning('The PHP code had a erreur : ' . $functions . ' is detected');
			}
		}
		// It's here because it's to check all function befor return to put all error in Grav Log
		if ($findDenyFunction)
		{
				return false;
		}

		// Check PHP Code Execution
		$out = '';
		ob_start();
			// Execution of code without consequences
			eval('if(false){' . $code . '}');
			$out = ob_get_contents();
		ob_end_clean();

		// If Erreur, the code is not executed
		if ($out)
		{
			$this->gravDebug->addMessage('CadPHP => The PHP code had a erreur : ' . $path);
			$this->gravLog->warning('CadPHP => The PHP code had a erreur : ' . $path);
			return false;
		}

		return true;
	}
}
