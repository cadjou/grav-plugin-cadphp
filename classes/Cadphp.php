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
	
	public function __construct(Grav $grav, Array $config)
    {
		$this->gravLog       = $grav['log'];
		$this->configPlugin  = $config;
		$this->docRoot		 = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';
		$this->pathPlugin	 = __DIR__ . '/..';
    }
	
	public function process(String $content)
	{
		$config	 = $this->configPlugin;
		
		$tabInclude = $this->calculPhp($content);

		foreach($tabInclude as $identifiant => $path)
		{
			$retour_php		= $this->safeEval($path)  ? include($path) : '';
			$retour_php		= is_string($retour_php)  ? $retour_php    : '';
			
			$content		= str_replace($identifiant,$retour_php,$content);
		}
		
		return $content;
	}
	
	public function calculPhp(String &$content)
	{
		$config	 = $this->configPlugin;
		
		$regex	 = '/^cadphp:(p\d{1,2}):(\S*)/m';
		preg_match_all($regex, $content, $results, PREG_SET_ORDER, 0);
		
		$return = [];
		
		foreach($results as $result)
		{
			if (!empty($result[0]))
			{
				$identifiant  = $result[0];
				$uniqueID	  = uniqid('cadjouphp_');
				$replace	  = (!empty($result[1]) and !empty($config[$result[1]]) and !empty($result[2])) ? $uniqueID  : '';
				
				$tabContent   = explode($identifiant,$content);
				$contentFirst = $tabContent[0];
				
				unset($tabContent[0]);
				
				$contentLasts = $tabContent ? implode($identifiant,$tabContent) : '';
				$content      = $contentFirst . $replace . $contentLasts;
				
				if ($replace)
				{
					$p				  = rtrim($config[$result[1]],'/');
					$racin		      = substr($p,0,1) == '/' ? $this->docRoot : $this->pathPlugin;
					
					$return[$replace] = $racin . '/' . $p . '/'. $result[2] . '.php'; 
				}
			}
		}
		return $return;
	}
	
	public function safeEval(String $path)
	{
		if (!is_file($path))
		{
			return false;
		}
		$config	 = $this->configPlugin;
		
		$code    = file_get_contents($path);
		$code    = str_replace('<?php','',$code);
		$code    = str_replace('?>'   ,'',$code);
		
		$tab_function_deny = isset($config['functions_deny']) ? $config['functions_deny'] : [];
		$tab_function_deny = (is_array($tab_function_deny))   ? $tab_function_deny        : [];
		
		foreach($tab_function_deny as $functions)
		{
			$regex = '/\s' . $functions . '\s*\(/m';
			preg_match_all($regex, $code, $find, PREG_SET_ORDER, 0);
			if ($find)
			{
				$this->gravLog->warning('The PHP code had a erreur : ' . $functions . ' is detected');
				return false;
			}
		}
		
		$out = '';
		ob_start();
			eval('if(false){' . $code . '}');
			$out = ob_get_contents();
		ob_end_clean();

		if ($out);
		{
			$this->gravLog->warning('CadPHP => The PHP code had a erreur : ' . $path);
			return false;
		}
		
		return true;
	}
}