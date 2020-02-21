<?php
namespace Grav\Plugin;
use \Grav\Common\Plugin;
use \Grav\Common\Grav;
use \Grav\Plugin\Cadphp\Cadphp;
use \RocketTheme\Toolbox\Event\Event;
use Grav\Common\Yaml;

class CadphpPlugin extends Plugin
{
	public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized'      => ['onPluginsInitialized', 0],
            'onFormProcessed'           => ['onFormProcessed', 0]
        ];
    }
	
	public function onPluginsInitialized()
    {
		require __DIR__ . '/classes/Cadphp.php';
		
        // No for Admin Page
        if ($this->isAdmin())
		{
            return;
        }

        // Enable the check of the content in the page call
        $this->enable([
			'onPageContentRaw' => ['onPageContentRaw', 0],
			]);
    }
	 
	 /**
     * From http://learn.getgrav.org/plugins/event-hooks
     *
     * @param Event $e
     */
    public function onPageContentRaw(Event $e)
    {
		$data = Yaml::parse($e['page']->frontmatter());
		$cadPHPHeader = (is_array($data) and isset($data['cadphp'])) ? $data['cadphp'] : null;
		
		$Cadphp  = new Cadphp($this->grav, $this->config());
		
		$content = $e['page']->getRawContent();
		$content = $Cadphp->processContent($content,$cadPHPHeader);
		
		$e['page']->setRawContent($content);
    }
	
	
    public function onFormProcessed(Event $event)
    {
        $form = $event['form'];
        $action = $event['action'];
        $params = $event['params'];
		
		if ($event['action'] <> 'cadphp')
		{
			return;
		}

		$twig	= $this->grav['twig'];
		$Cadphp = new Cadphp($this->grav, $this->config());
		
		foreach($Cadphp->processForm($params,$form->getValue('data')) as $key=>$data)
		{
			$twig->twig_vars['cadphp'][$key] = $data;
		}
    }
	
	public static function dataProcess($data = [])
    {
		$Grav = Grav::instance();
		$Cadphp  = new Cadphp($Grav, $Grav['config']->get('plugins.cadphp'));
		return $Cadphp->processData($data);
	}
}