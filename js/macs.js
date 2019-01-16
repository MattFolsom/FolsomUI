function setupMenu () {
	var menuHTML = "";
	menuHTML += "<span class=\"menuLeft\"><em id=\"btnDashboard\">Dashboard</em><em id=\"btnUsers\">Users</em><em id=\"btnMachines\">Machines</em><em id=\"btnLog\">Log</em></span>"
	menuHTML += "<span class=\"menuRight\"><input id=\"refRate\" placeholder=\"Refresh rate (seconds) \" style=\"display:none;\"/><em id=\"btnRefRate\" style=\"display:none;\"></em><em id=\"btnLogOut\">Log Out</em></span>"
	$("#menu").html(menuHTML);
	
	
				$("#btnDashboard").kendoButton({
					icon: "calculator",
					click: function () {location.href = "index.php";}
				});
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

}
//all-purpose logging function
function WriteLog(user_id, machine_id, eventTxt, login_id){ 
	var datapkg ="user_id="+user_id+"&machine_id="+machine_id+"&event="+eventTxt+"&login_id="+login_id
	$.ajax({
		url: 'json_Log.php', 
		method: 'PUT', 
		data: datapkg, // data as js object
		success: function() {}
	});
}