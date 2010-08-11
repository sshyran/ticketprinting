/*
 * Global variables
 */
// Set the global variable generate_called to false.  This ensures taht the 
// main if clause in eval_count() will run at least once.
var generate_called = false;

//global variable print_called set to false to begin with to ensure that print.php is only called once.
var print_called = false;

// set the number of pages to be created by one makepng.php ajax call.
var pages = 18;

// set the number of images generated by the above number of pages given 3 images per page.
var per_image = Number(pages)*Number(3);

// set the number of pdf pages to be created for each pdf.
var pdf_pages = 28;

// set the number of images in each pdf given the above number of pages, and 3 images per page
var per_pdf = Number(pdf_pages)*Number(3);
/*
 * Function to create tickets.  It is called when the user presses submit on the home page.
 */

$(document).ready(function() {
	
	$("#create").click(create);
	$(".ticket-radio").click(function(){
		var count = $(this).closest('tr').find('.row_count').text();
		$("input[name=start]").val(function(){
			return (Number(count) + Number(1));
			});
		});

function validate(){
	// check if the user has selected a ticket.
	if($("input[name='ticket']:checked").length == 1){ //if a ticket is checked
		if(Number($("input[name='num']").val()) != "" && Number($("input[name='num']").val()) != null){ //if number of tickets is not empty or null
			return true;
		}else{ // if the user hasn't specified a number of tickets, error out.
			alert("Please enter the number of tickets you would like to create.");
		}
	}else{ // if the user hasn't selected a ticket, error out.
		alert("You haven't selected a ticket to work with");
	}
}

function create(){
	// set the global variables to false so that the script can run multiple times without a page refresh.
	generate_called = false;
	print_called = false;
	
	// first call the cleanup script to make sure that there are no dangling png, pdf or zip files
	// and only execute after that script has returned successful.
	$.get("cleanup.php",{submit:"submit"},function(data){
		$("#print_bt").hide();
		$("#print_ready").hide();
		$("#print_sent").hide();		
		//validate input. checks that the user has selected and entered required info and if so, calls the function to do the ticket creation		
				
		if(validate() == true){
			do_creation();
		}		
	});
}



function do_creation(){
	// validate that a ticket has been selected to work with
	var ischecked  = $("input[name='ticket']:checked").length;
	
	// get the starting ticket number.  This will be the first ticket printed
	var start = $("input[name='start']").val();
	
	// get the number of tickets desired.
	var num = Number($("input[name='num']").val());
	
	// calculate the number of tickets that will be created (number desired minus starting number)
	var tot = Number(num) + Number(start) - Number(1);
	
	// get the text the user wants placed to the left of the ticket number
	var t = $("input[name='t']").val();
	
	// get the font the user wants for the above text and ticket number
	var f = $("select[name='f']").val();
	
	// get the watermark text the user wants
	var wt = $("input[name='wt']").val();
	
	// get the font for the watermark
	var wf = $("select[name='wf']").val();
	
	// get the ticket the user wants to work with
	var ticket = $("input[name='ticket']:checked").val();	
	
	// empty the text generated by any previous runs
	$("#pdfp").empty();
	$("#imgp").empty();
	$("#printp").empty();

	// Show the loading image.
	$("#loading").show();

	// set the count variable equal to start.
	var count = start;

	// show the #images div
	$("#images").fadeIn();
	
	// iterate through from start to tot
	for(i=start;i<=tot;i++){
		/* call makepng.php submitting all the variable we just aquired from the form.
		 * make png does not return anything.  It simply outputs a  group of large images that have
		 * three tickets each, all numbered, and if applicable, watermarked.
		 * 
		 * The pages value below is manually set as a global varaible, currently 18, which generates 54 tickets.  
		 * makepng.php knows to stop making images when it reaches tot.  So just because
		 * we request 54 images at a time does not mean excess images will be created.
		 */
			$.get("/makepng.php", { submit:"submit", pages:pages, q: i, f: f, img: ticket, t: t, wt: wt, wf: wf,tot:tot },
				function(data){
					// for each call that returns successful, add 54 to the count.
					// count basically keeps track of how many tickets have been requested.
					// it is used later for the progress bar.
				 	count = Number(count) + Number(per_image);
				 	// call eval_count() for each successful ajax call.  
					eval_count(count,num,tot,start,ticket);		
				});
			// increase i by 53.  Note that it is not 54 because the for loop has already increased it by 1 so we compensate by subtracting 1.	
			i = Number(i) + Number(per_image) - Number(1);
	}
}


/*
 * funcion eval_count() 
 * This function evaluates whether the count has reached or passed the total or not.
 * On each call, it updates the progress bar to give the user some feedback  
 * When it succedes, it calls the next function in the chain, initiating the creation of pdf files.
 * 
 * @param count The current count to be checked
 * @param num The number of tickets requested.  This variable is passed to other functions.
 * @param tot The number of the last ticket to be created.
 * @param start The starting number of tickets.  Passed to other functions.
 * @param ticket The path/filename of the ticket's image file.  Passed to other functions.
 */
function eval_count(count,num,tot,start,ticket){
	// check if count is greater than or equal to total.  Count increases by 54 after each call
	// so it is assumed that it will be greater than total.
    if(count >= tot){
    	
    	// if the generate_called() function has not yet been called, then call it.  We only want to call that function once.
        if(generate_called != true){
        	
        	// hide the loading image
			$("#loading").hide();
			$("#imgp").empty();
			
			// call the generate function, which turns the png images into multi-page pdfs
			generate(num,start,tot,ticket);
   		
        }
        // set generate_called to true so that we won't call it again.
        generate_called = true;
    } else { // if the count is not yet greater than or equal to the total, keep generate_called false.
		generate_called = false;
    }
    
    // update the progress bar after each ajax call comes back successful. 
	$("#progress").progressbar({value: (Number(count)/Number(tot)*Number(100))});
    
}


/*
 * function generate()
 * This function is called by eval_count() when it succedes.
 * It generates mult-page pdfs from the png files created by the create() function.
 * @param num The number of tickets requested.  This variable is passed to other functions.
 * @param start The starting number of tickets.  Passed to other functions.
 * @param tot The number of the last ticket to be created.
 * @param ticket The path/filename of the ticket's image file.  Passed to other functions.
 */
function generate(num,start,tot,ticket){
	
	// set a local variable count equal to start.
	var count = start;
	
	// show the #pdf div
	$("#pdf").show();
	
	// iterate through from start to total
	for(i=Number(start);i<=tot;i++){
		
		/* Call makepdf.php.
		 * Makepdf.php only takes one real variable: start.
		 * This is not to be confused with the javascript variable start that we have been dealing with.
		 * start refers to a variable in the php script. We pass the i from the for loop as the variable start. 
		 * Much like makepng.php, makepdf.png does not return anything.  It looks through the png files created by
		 * create(), and adds them, 84 at a time to a pdf.
		 */;
		$.get("makepdf.php",{submit:"submit",start:i},function(data){
			// increase the local count variable by 83 (it has already been increased by 1).
			count = Number(count) + Number(per_pdf) - Number(1);
			
			// call eval_pdf_count() to see if the script is done generationg pdfs.
			eval_pdf_count(count,num,start,tot,ticket);
		});
		// increase i by 83.
		i = Number(i) + Number(per_pdf) - Number(1);
	}	
}

/*
 * function eval_pdf_count()
 * This function checks whether enough pdfs have been generated to accomodate the number of tickes desired.
 * If so, the user is given the option to print the pdf's
 * 
 * @param count The current count to be checked
 * @param num The number of tickets requested.  This variable is passed to other functions.
 * @param start The starting number of tickets.  Passed to other functions.
 * @param tot The number of the last ticket to be created.
 * @param ticket The path/filename of the ticket's image file.  Passed to other functions.
 */
function eval_pdf_count(count,num,start,tot,ticket){

	// calculate the last ticket to be printed minus 3.  We use this to prevent extra pdfs from being generated.
	numless = Number(num)-Number(3);
	if(count >= numless){
		
		// first check if print_called is true.  We only want to call it once.
		if(print_called != true){
			//hide the loading image for pdfs.
			$("#loadingpdf").hide();

			//Give user a print option here before calling print function. also, tell them it's ready to print.(bc)	
			$("#print_ready").show();
			$("#print_bt").show();

			$("#print_bt").bind('click',function () {
				print_watch(num, ticket);
			});

			// show the progress info for printing.
			//$("#loadingprint").show();
			//$("#printp").empty();
			//$("#print").show();
			
			print_called = true;
		}
	} else {
		//print_called = false;
	}
	
	// update the progress bar, though this is a little silly since it will jump from 0 straight to 100.
	$("#progress").progressbar({value: (Number(count)/Number(num)*Number(100))});
	
}

function print_watch(num, ticket){
	// call print.php.
	$.get("print.php",{print_bt:"print"},function(data){ //make this responed to a print button and not the submit button (bc)
	
		// when print.php returns successful, hide the loading image (commented out by bc)
		//$("#loadingprint").hide();
	
		// and show the link to download the zip file (commented out by bc)
		//$("#printp").show().append("PDF files finished and printed.");
	
		// then call update_count to 
		update_count((Number(num)),ticket);
		$("#print_bt").unbind();
		$("#print_bt").hide();
		$("#print_ready").hide();
		$("#print_sent").show();
	});
	
}
/*
 * function update_count()
 * Very straightforward function that calls updatecount.php and updates the number
 * of tickets that have been printed so far.
 * 
 * @param count  The number of tickets printed in this round.
 * @param ticket The path/filename of the ticket we've operated on
 */
function update_count(count,ticket){
	$.getJSON("updatecount.php",{count:count,file:ticket},function(data){
		
		// update the count in the ticket's description
		$("body").find("#count_"+ data.id).empty().text(data.count).fadeIn('slow');
		// update the starting number in the start field
		$("input[name=start]").val(Number(data.count) + Number(1));
	});
}
});
