<?php
session_start();
?>

<html manifest="offline.manifest">
<head>
<title>Clients</title>
<p id="status">Online</p>
<style>
    [data-slots] { font-family: monospace }
</style>

<script type="text/javascript" src="jquery-1.4.min.js"></script>
<script type="text/javascript" src="offline.js"></script>

<?php
    include "dbConnections.php";
    if(!isset($_SESSION['cart']))
    {
        header('Location: ManageSupplement.php');
    }
    if(!isset($_SESSION['Client_id']))
    {
        header('Location: ManageClients.php');
    }
    $sql = "SELECT `Inv_Num` FROM `tblinv_info` ORDER BY Inv_Num DESC LIMIT 1";
    $result = $conn->query($sql);
    $invoiceNumber = "";
			
    if($row = $result->fetch_assoc()) 
    {		
        $invoiceNumber = $row['Inv_Num'];
        $numberPart =  str_replace("INV","",$invoiceNumber );
        $numberPart = (int)$numberPart;
        $numberPart = $numberPart+1;
        $_SESSION['invoiceNumber']= 'INV'.$numberPart;
    }
        else
        {
            $_SESSION['invoiceNumber'] = "INV0001";
        }
			
        $_SESSION['invoiceDate'] = date_create('now')->format('Y-m-d');
        $sql = "";
        $sql = "INSERT INTO `tblinv_info`(`Inv_Num`,`Client_id`, `Inv_Date`, `Inv_Paid`, `Inv_Paid_Date`, `Comments`)".
	" VALUES ('".$_SESSION['invoiceNumber']."','".$_SESSION['Client_id']."','".$_SESSION['invoiceDate']."','N','".$_SESSION['invoiceDate']."','')";
        if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
        } else {
             echo "Error: " . $sql . "<br>" . $conn->error;
            }

?>
<script type="text/javascript">
// This code empowers all input tags having a placeholder and data-slots attribute
document.addEventListener('DOMContentLoaded', () => {
for (const el of document.querySelectorAll("[placeholder][data-slots]")) {
const pattern = el.getAttribute("placeholder"),
slots = new Set(el.dataset.slots || "_"),
prev = (j => Array.from(pattern, (c,i) => slots.has(c)? j=i+1: j))(0),
first = [...pattern].findIndex(c => slots.has(c)),
accept = new RegExp(el.dataset.accept || "\\d", "g"),
clean = input => {
input = input.match(accept) || [];
return Array.from(pattern, c =>
input[0] === c || slots.has(c) ? input.shift() || c : c);
},
format = () => {
const [i, j] = [el.selectionStart, el.selectionEnd].map(i => {
i = clean(el.value.slice(0, i)).findIndex(c => slots.has(c));
return i<0? prev[prev.length-1]: back? prev[i-1] || first: i;
});
el.value = clean(el.value).join``;
el.setSelectionRange(i, j);
back = false;
};
let back = false;
el.addEventListener("keydown", (e) => back = e.key === "Backspace");
el.addEventListener("input", format);
el.addEventListener("focus", format);
el.addEventListener("blur", () => el.value === pattern && (el.value=""));
}
});
</script>
</head>

<body>
<div name="invoiceContent" id="invoiceContent">
<h1>Invoice</h1>

<?php
    echo "<table>";
    echo "<tr><th>Invoice Number:</th><td>".$_SESSION['invoiceNumber']."</td></tr>";
    echo "<tr><th>Client ID:</th><td>".$_SESSION['Client_id']."</td></tr>";
    echo "<tr><th>Name:</th><td>".$_SESSION['C_name']." ".$_SESSION['C_surname']."</td></tr>";
    echo "<tr><th>Date:</th><td>".date_create('now')->format('Y-m-d')."</td></tr>";
    echo "<tr>";
    echo "<td><h3>Invoice Items</h3></td>";
    echo "</tr>";
    echo "<tr>";
    echo "</table>";
    echo "<table border='1'>";
    echo "<th>Supplement name</th><th>Items to checkout</th>";
    echo "</tr>";
    if(isset($_SESSION['cart']))
    {
        $totalCount=0;
	$invoiceTotalInclVat = 0;
	$invoiceTotalExclVat = 0;
				
	echo "<tr>";
	echo "<td>Supplement Id</td><td>Supplement Description</td><td>Cost Excluding Vat</td><td>Cost Including Vat</td><td>Cost Excluding Vat</td><td>Total Excluding Vat</td><td>Quantity</td><td>Total Including Vat</td>";
	echo "</tr>";
	foreach ($_SESSION['cart'] as $key => $value) 
	{
            echo "<tr>";
            $sql = "SELECT Supplement_id,Supplier_Id,Supplement_Description,Cost_excl,Cost_incl,Min_level,Current_stock_levels,Nappi_code
            FROM tblsupplements  WHERE Supplement_id ='".$key."'";
            $result = $conn->query($sql);
            $totalCount=$totalCount+$value;
            while($row = $result->fetch_assoc()) 
            {		
                $sumInclVat = ($row['Cost_incl']*$value);
                $sumExclVat = ($row['Cost_excl']*$value);	
                $invoiceTotalInclVat=$invoiceTotalInclVat+$sumInclVat;					
                $invoiceTotalExclVat=$invoiceTotalExclVat+$sumExclVat;					
                echo "<tr>";
                echo "<td>".$key."</td><td>".$row['Supplement_Description']."</td><td>".$row['Cost_excl']."</td><td>".$row['Cost_incl']."</td><td>".$row['Cost_excl']."</td><td>R".$sumExclVat."</td><td>".$value."</td><td>R".$sumInclVat."</td>";
                echo "</tr>";
            }
	}
        echo "<tr></tr>";
        echo "</table>";
        echo "<table>";
        echo "<tr>";
        echo "<th>Quantity:</th><td>".$totalCount."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<th>Total Excluding Vat:</th><td>R".$invoiceTotalExclVat."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<th>Total Including Vat:</th><td>R".$invoiceTotalInclVat."</td>";
        echo "</tr>";
    }
    else
        {
            echo "<tr>";
            echo "<td colspan='2'>No Items in the cart</td>";
            echo "</tr>";
        }
        echo "</table>";
    $conn->close();
    ?>
    <div>
</body>
</html>