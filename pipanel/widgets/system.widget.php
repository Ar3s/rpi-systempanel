<?php
	require_once('abstract.widget.php');

	class SystemWidget extends AbstractWidget
	{
		public $host;
		public $current_time;
		public $system;
		public $kernel;
		public $uptime;
		
		public function load()
		{
			$this->current_time = exec("date +'%d %b %Y<br />%T %Z'");
			$this->frequency = number_format(exec("cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq") / 1000);
			
			list($this->system, $this->host, $this->kernel) = preg_split("/\s/", exec("uname -a"), 4);
			
			$uptime_array = explode(" ", exec("cat /proc/uptime"));
			$seconds = round($uptime_array[0], 0);
			$minutes = $seconds / 60;
			$hours = $minutes / 60;
			$days = floor($hours / 24);
			$hours = sprintf('%02d', floor($hours - ($days * 24)));
			$minutes = sprintf('%02d', floor($minutes - ($days * 24 * 60) - ($hours * 60)));
			
			$this->uptime = ($days <= 0 ? $days . "d " : "") .  $hours . "h " .  $minutes . "m";
		}
	}
	
	$widget = new SystemWidget();
?>