<!DOCTYPE html>
<?php
// Initialize the session
session_start();
 
// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: login.php");
  exit;
}
?>
<html>
	<head>
		
		<title>MACS-Access(<?php echo $_SESSION['username'] ?>)</title>
		<!-- 
		
		This page is a access-centric page that will allow the user to search, filter, sort MACS users and associated machines.  
		References to kendo.cdn.telerik.com enable the Procress Telerik Kendo UI features under the Apache v2.0 License.  
		
		-->
		
		<link rel="icon" href="images/MB_Favicon.png">
		<link rel="stylesheet" href="//kendo.cdn.telerik.com/2018.2.516/styles/kendo.common.min.css" />
		<link rel="stylesheet" href="//kendo.cdn.telerik.com/2018.2.516/styles/kendo.office365.min.css" />
		<link rel="stylesheet" href="//kendo.cdn.telerik.com/2018.2.516/styles/kendo.default.mobile.min.css" />
		<!-- Load Pako ZLIB library to enable PDF compression -->
		<script src="https://kendo.cdn.telerik.com/2019.1.115/js/pako_deflate.min.js"></script>
		<script src="js/jszip.min.js"></script>
		<link rel="stylesheet" href="css/macs.php" type="text/css"/>

		<script src="//kendo.cdn.telerik.com/2018.2.516/js/jquery.min.js"></script>
		<script src="//kendo.cdn.telerik.com/2018.2.516/js/kendo.all.min.js"></script>
		<script src="js/macs.js"></script>

		<style>
        /*
            Use the DejaVu Sans font for display and embedding in the PDF file.
            The standard PDF fonts have no support for Unicode characters.
        */
        .k-grid {
            font-family: "DejaVu Sans", "Arial", sans-serif;
        }
		     /* Page Template for the exported PDF */
        .page-template {
          font-family: "DejaVu Sans", "Arial", sans-serif;
          position: absolute;
          width: 100%;
          height: 100%;
          top: 0;
          left: 0;
        }
        .page-template .header {
          position: absolute;
          top: 30px;
          left: 30px;
          right: 30px;
          border-bottom: 1px solid #888;
          color: #888;
        }
        .page-template .footer {
          position: absolute;
          bottom: 30px;
          left: 30px;
          right: 30px;
          border-top: 1px solid #888;
          text-align: center;
          color: #888;
        }
        .page-template .watermark {
          font-weight: bold;
          font-size: 400%;
          text-align: center;
          margin-top: 30%;
          color: #aaaaaa;
          opacity: 0.1;
          transform: rotate(-35deg) scale(1.7, 1.5);
        }
		
		/* Override some CSS for better printing in the PDF */
		.k-grid-content>table>tbody>.k-alt{background:rgba(99,99,99, 0.2);}
		.k-grid tbody tr{height:20px;}
		.k-grid tbody td{padding:0px;}
    </style>

    <script>
        /*
            This renders the grid in "DejaVu Sans" font family, which is
            declared in kendo.common.css. It also declares the paths to the
            fonts below using <tt>kendo.pdf.defineFont</tt>, because the
            stylesheet is hosted on a different domain.
        */
        kendo.pdf.defineFont({
            "DejaVu Sans"             : "https://kendo.cdn.telerik.com/2016.2.607/styles/fonts/DejaVu/DejaVuSans.ttf",
            "DejaVu Sans|Bold"        : "https://kendo.cdn.telerik.com/2016.2.607/styles/fonts/DejaVu/DejaVuSans-Bold.ttf",
            "DejaVu Sans|Bold|Italic" : "https://kendo.cdn.telerik.com/2016.2.607/styles/fonts/DejaVu/DejaVuSans-Oblique.ttf",
            "DejaVu Sans|Italic"      : "https://kendo.cdn.telerik.com/2016.2.607/styles/fonts/DejaVu/DejaVuSans-Oblique.ttf",
            "WebComponentsIcons"      : "https://kendo.cdn.telerik.com/2017.1.223/styles/fonts/glyphs/WebComponentsIcons.ttf"
        });
    </script>
		<script type="x/kendo-template" id="page-template">
			<div class="page-template">
				<div class="header">
				  <div style="float: right">Page #: pageNum # of #: totalPages #</div>
				  User Access List
				</div>
			<div class="watermark">The Maker Barn</div>
				<div class="footer">
				  Page #: pageNum # of #: totalPages #
				</div>
			</div>
		</script>
		<script>
			$(document).ready(function () {
				setupMenu(); //Creates navigation buttons at top of page
				getAccessDataSource(); //Build the Kendo datasource object
				makeAccessGrid (); //Build the main Kendo grid of access.
				
				//jQuery for menu buttons - TODO: move this to macs.js
				$("#btnUsers").kendoButton({
					icon: "user",
					click: function () {location.href = "UserGrid.php";}
				});
				$("#btnMachines").kendoButton({
					icon: "gears",
					click: function () {location.href = "MachGrid.php";}
				});
				$("#btnLog").kendoButton({
					icon: "clock",
					click: function () {location.href = "LogGrid.php";}
				});
				$("#btnLogOut").kendoButton({
					icon: "logout",
					click: function () {location.href = "logout.php";}
				});
				
				
			});
			
			function getAccessDataSource (){
				AccessDataSource = new kendo.data.DataSource({
					transport: {
						read: {
							url: "json_Access.php",	
							dataType: "jsonp", // "jsonp" is required for cross-domain requests; use "json" for same-domain requests,
							jsonpCallback: 'Access',
							type: "GET"
						}
					},
					schema: {
                        model: {
							id: "id",
                            fields: {
                                id: {type: "string"},
                                userName: {type: "string"},
                                machName: {type: "string"},
                                machDesc: {type: "string"}
                            }
                        }
                    },
					group:{field:"userName"},
					pageSize: 50
				});	
			}
			
			function makeAccessGrid (){
				
				 $("#gridAccess").kendoGrid({
					toolbar: ["pdf"],
					pdf: {
						allPages: true,
						avoidLinks: true,
						paperSize: "A4",
						margin: { top: "2cm", left: "1cm", right: "1cm", bottom: "1cm" },
						landscape: true,
						repeatHeaders: true,
						template: $("#page-template").html(),
						scale: 0.8
					},
					dataSource: AccessDataSource,
					sortable: true,
					selectable: "row",
					filterable: {mode: "row"},
					reorderable: true,
					groupable: true,
					pageable: {
						refresh: true,
						pageSizes: true,
						buttonCount: 5
					},
					columns: [
						{
						field: "id",
						title: "ID",
						width: 5
						},{
						field: "userName",
						title: "User Name",
						sortable: {initialDirection: "asc"},
						filterable: {cell: {operator: "contains"}},
						width: 25
						},{
						field: "machName",
						title: "Machine Name",
						sortable: {initialDirection: "asc"},
						filterable: {cell: {operator: "contains"}},
						width: 25
						},{
						field: "machDesc",
						title: "Machine Description",
						sortable: {initialDirection: "asc"},
						filterable: {cell: {operator: "contains"}},
						width: 25
						
					}]
				});
				$("[date-text-field='name'] ").focus(); //set initial focus on the name search/filter box
			}
		</script>

		<style type="text/css">
			button {
				height:20px;
				width:40px;
				
			}
			#primary {
				max-width:1200px;
				margin:auto;
			}

			.SelectedTitle {
				color:#00b0ff;
			}
			table {margin:auto;}
		</style>
	</head>
<body>
<div id="master" class="Content">
	<div id="menu"></div>
    <div id="gridAccess"><h2>MACS Users-Machines<a id="titleSelected" class="SelectedTitle" title=""></a></h2></div>

</div>
</body>
</html>
