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
	
    /** @var keyWord */
    protected $keyword = 'cadphp';
	
	public function __construct(Grav $grav, $config)
    {
		$this->gravLog       = $grav['log'];       // Add the PHP Error in case
		$this->configPlugin  = $config;            // Get the Configuration Plugin in cadphp/cadphp.yaml and cadphp/blueprints.yaml
		
		$this->docRoot		 = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';
		$this->pathPlugin	 = __DIR__ . '/..';
    }
	
	public function process($content)
	{
		// Get the keyword and replace by ID in content and return Array ID => Path
		$tabInclude = $this->calculPhp($content);

		foreach($tabInclude as $identifiant => $path)
		{
			// Check the path and include it to the code
			$return_php		= $this->safeEval($path)  ? include($path) : '';
			// Check if it's possible to print it
			$return_php		= is_string($return_php)  ? $return_php    : '';
			// Replace th ID by the PHP Code Return
			$content		= str_replace($identifiant,$return_php,$content);
		}
		return $content;
	}
	

	public function calculPhp(&$content)
	{
		$config	 = $this->configPlugin;
		
		// Regex to to find the Keyword by default is cadphp
		$regex	 = '/^' . $this->keyword . ':(p\d{1,2}):(\S*)/m';
		preg_match_all($regex, $content, $results, PREG_SET_ORDER, 0);
		
		$return = [];
		
		foreach($results as $result)
		{
			if (!empty($result[0]))
			{
				// Get the data and check if the data are good
				$identifiant  = $result[0];
				$uniqueID	  = uniqid('cadjouphp_');
				$replace	  = (!empty($result[1]) and !empty($config[$result[1]]) and !empty($result[2])) ? $uniqueID  : '';
				
				// Use explode to replace only this keyword
				$tabContent   = explode($identifiant,$content);
				// Get the beginning
				$contentFirst = $tabContent[0];
				// Get the ending
				unset($tabContent[0]);
				$contentLasts = $tabContent ? implode($identifiant,$tabContent) : '';
				
				// Change Content with the unique ID and remove the path if it's bad data
				$content      = $contentFirst . $replace . $contentLasts;
				
				if ($replace)
				{
					// Get the Predefined Path from keyword PXX (00 - 99)
					$p				  = rtrim($config[$result[1]],'/');
					// Get the racin path in depend of the beginning of the Predefined Path (Absolu or Relative Path)
					$racin		      = substr($p,0,1) == '/' ? $this->docRoot : $this->pathPlugin;
					// Return Array ID => Path
					$return[$replace] = $racin . '/' . $p . '/'. $result[2] . '.php'; 
				}
			}
		}
		
		return $return;
	}
	
	public function safeEval($path)
	{
		if (!is_file($path))
		{
			$this->gravLog->warning('The path ' . $path . ' is not a file');
			return false;
		}
		$config	 = $this->configPlugin;
		
		// Get the PHP and Remove the PHP Balises
		$code    = file_get_contents($path);
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
			$this->gravLog->warning('CadPHP => The PHP code had a erreur : ' . $path);
			return false;
		}
		
		return true;
	}
}