<html>
	<head>
		<title>STDL - Article recommender</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="../style.css" />
		<script rel="text/javascript" src="./script/jquery/js/jquery-1.10.2.js"></script>
		<link rel='stylesheet' type='text/css' href='./script/jquery/css/ui-lightness/jquery-ui-1.10.4.custom.css'/>
		<link rel='stylesheet' type='text/css' href='./script/jquery/development-bundle/themes/base/jquery-ui.css'/>
		<script rel="text/javascript" src="./script/jquery/js/jquery-ui-1.10.4.custom.min.js"></script>
		<script type="text/javascript">
			$(function() {
				$( "input[type=submit]" ).button()
			      .click(function( event ) {
			          
			        });
				
				/*
				$( "table tbody td:first-of-type" ).button();
				$( "table tbody td:first-of-type span" ).css("background-color", "white").css("text-align", "left");
				*/
			});
			
		</script>
		<style type="text/css">
			textarea {
				margin: 20px;
			}
			
			#results{
				border-collapse: collapse;
				margin-top: 50px;
			}
			
			#results tbody td{
				padding: 15px;
			}
			
			#results tbody tr{
				background-color: #eee;
    			border-top: 1px solid #fff;
			}
			
			#results tbody tr:nth-child(2n+1){ 
				/*background-color: #eee;*/
			}
			
			#results tbody tr{
				background-color: transparent;
    			border-top: 1px solid #fff;
			}
			
			#results tbody tr:hover{
				background-color: ghostwhite;
			}
			
			.abstract {
				font-size: 90%;
				margin-left: 10px;
			}
			
			sub{
				font-size: 80%;
			}
			
			.doi{
				color: gray;
			}
			
			h3{
				color: #2a6496;
			}
			
			h3 a{
				color: #2a6496;
				text-decoration: none;
			}
			
			h3 a:hover{
				color: #2a6496;
				text-decoration: underline;
			}
		</style>
	</head>
	<body>
		<div>
			<h1><img src="../STDL_logo_rev_FA1.png" /></h1>
			<div style="text-align: center;">
				<h2>Article recommender</h2>
				<form>
					<textarea rows="5" cols="80" name="query"></textarea><br/>
					<input type="submit" value="search" style>
				</form>
				
				{$table}

			</div>
			
		</div>
	</body>
</html>