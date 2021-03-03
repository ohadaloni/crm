/*------------------------------------------------------------*/
$(function() {
	crmPaintRows(document);
	/*	$(".imgToolTip").imgToolTip();	*/
	$(".showImage").showImage();
});
/*------------------------------------------------------------*/
function crmPaintRows(context)
{
	$(".mRow", context).hoverClass("hilite");
	$(".crmRow", context).hoverClass("hilite");
	$(".mFormRow", context).hoverClass("hilite");
	$(".mHeaderRow", context).addClass("crmZebra0");
	$(".crmHeaderRow", context).addClass("crmZebra0");
	$(".mFormRow:nth-child(odd)", context).addClass("crmZebra1");
	$(".mFormRow:nth-child(even)", context).addClass("crmZebra2");
	$(".mRow:nth-child(odd)", context).addClass("crmZebra1");
	$(".mRow:nth-child(even)", context).addClass("crmZebra2");
	$(".crmRow:nth-child(odd)", context).addClass("crmZebra2");
	$(".crmRow:nth-child(even)", context).addClass("crmZebra1"); // first row is 1
	$(".crmFormRow:nth-child(odd)", context).addClass("crmZebra2");
	$(".crmFormRow:nth-child(even)", context).addClass("crmZebra1"); // first row is 1

	$(".today:nth-child(odd)", context).addClass("crmZebra3");
	$(".today:nth-child(even)", context).addClass("crmZebra4");
	$(".yesterday:nth-child(odd)", context).addClass("crmZebra5");
	$(".yesterday:nth-child(even)", context).addClass("crmZebra6");

}
/*------------------------------------------------------------*/
