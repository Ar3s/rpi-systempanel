<div class="network_grid"></div>
<script type="text/javascript">
	$(document).ready(function () {
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'name' },
                { name: 'encap' },
                { name: 'mac'},
                { name: 'ip' },
                { name: 'rx' },
                { name: 'tx' }
            ],
            id: 'id',
            url: 'widget_loader.php',
            root: 'nics',
            data: {
            	widget_id: {$user_widget_info->id},
            	sid: '{$sid}',
            	json: true,
            	featureClass: "P",
                style: "full",
                maxRows: 10
			},
			type: 'POST'
        };
        
        var dataAdapter = new $.jqx.dataAdapter(source);
        $("#{$user_widget_info->id_html} .network_grid").jqxGrid(
        {
        	showdefaultloadelement: false,
        	autoshowloadelement: false,
            width: '100%',
            source: dataAdapter,
            theme: 'bootstrap',
            columnsresize: true,
            pageable: true,
            autoheight: true,
            columns: [
              { text: 'Name', dataField: 'name', width: 50 },
              { text: 'Full Name', dataField: 'encap' },
              { text: 'MAC', dataField: 'mac', width: 120 },
              { text: 'IP', dataField: 'ip', width: 100 },
              { text: 'RX', dataField: 'rx', width: 70 },
              { text: 'TX', dataField: 'tx', width: 70 }
            ]
        });
        
        setTimeout(function () {
			updateNetworkGrid();
		}, {$widget_info->updatetime});
	});
	
	function updateNetworkGrid() {
		$("#{$user_widget_info->id_html} .network_grid").jqxGrid('updatebounddata', 'cells');
		
		setTimeout(function () {
			updateNetworkGrid();
		}, {$widget_info->updatetime});
	}
</script>