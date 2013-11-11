<?php
	require_once('settings.php');

	$settings = new Settings('settings.json');
	$settings->check_auth();

	abstract class AbstractWidget
	{
		public $template_file;
		
		public function __construct()
		{
			$this->load();
		}
		
		public function json()
		{
			json_encode($this);
		}
		
		public function html($smarty)
		{	
			$smarty->assign('widget', $this);
			
			$smarty->display($this->template_file);
		}
		
		abstract public function load();
	}
	
	$widget = null;
?>