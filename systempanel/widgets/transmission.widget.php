<?php
	require_once('abstract.widget.php');
	require('../framework/TransmissionRPC.class.php');

	class TransmissionWidget extends AbstractWidget
	{
		public $total_torrents;
		public $active_torrents;
		public $seeding_torrents;
		public $downloading_torrents;
		
		public function load()
		{
			$rpc = new TransmissionRPC();
	
			$result = $rpc->sstats();
			
			$this->total_torrents = $result->arguments->torrentCount;
			$this->active_torrents = $result->arguments->activeTorrentCount;
	
			$result = $rpc->get();
			$torrents = $result->arguments->torrents;
			$statuses = array();
	
			foreach ($torrents as $item)
			{
				$statuses[] = $rpc->getStatusString($item->status);
			}
			
			$counts = array_count_values($statuses);
			
			$this->seeding_torrents = isset($counts["Seeding"]) ? $counts["Seeding"] : 0;
			$this->downloading_torrents = isset($counts["Downloading"]) ? $counts["Downloading"] : 0;
		}
		
		public function stop()
		{
			$rpc = new TransmissionRPC();
			
			$rpc->stop(null);
		}
		
		public function start()
		{
			$rpc = new TransmissionRPC();
			
			$rpc->start(null);
		}
	}

	$widget = new TransmissionWidget();
	
	if (isset($_POST['sta'])) {
		$widget->start();
	} else if (isset($_POST['sto'])) {
		$widget->stop();
	}
?>