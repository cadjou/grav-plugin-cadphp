<?php
namespace Grav\Plugin;
use \Grav\Common\Plugin;
use \Grav\Plugin\Cadphp\Cadphp;
use \RocketTheme\Toolbox\Event\Event;

class CadphpPlugin extends Plugin
{
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
		$Cadphp  = new Cadphp($this->grav, $this->config());
		
		$content = $e['page']->getRawContent();
		$content = $Cadphp->process($content);
		
		$e['page']->setRawContent($content);
    }
	

}