<html>
<head>
<!--Load the AJAX API-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
// Load the Visualization API and the corechart package.
google.charts.load('current', {'packages':['corechart']});
// Set a callback to run when the Google Visualization API is loaded.
google.charts.setOnLoadCallback(drawChart);
// Callback that creates and populates a data table,
// instantiates the bar chart, passes in the data and
// draws it.
function drawChart() {
// Create the data table.
var data = new google.visualization.DataTable();
data.addColumn('string', 'Date');
data.addColumn('number', '# of Orders');
// This is where you will need to pass your SQL data to JavaScript
// I have not included this information, if needed, ask
data.addRows([
['01/02/17',1],
['01/03/17',4],
['01/04/17',9],
['01/05/17',6]
]);
// Set chart options
var options = {
'title':'Orders over Time',
'width':500,
'height':500};
// Instantiate and draw our chart, passing in some options.
var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
chart.draw(data, options);
}
</script>
</head>
<body>
<!--Div that will hold the pie chart-->
<div id="chart_div"></div>
</body>
</html>