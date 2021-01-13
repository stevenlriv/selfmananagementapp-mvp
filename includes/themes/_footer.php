<?php
	if ( !defined('THEME_LOAD') ) { die ( header('Location: /404') ); }
?>

<!-- Right Sidebar -->
<div class="side-bar right-bar">
    <div class="nicescroll">
        <ul class="nav nav-pills nav-justified text-xs-center">
            <li class="nav-item">
                <a href="#home-2"  class="nav-link active" data-toggle="tab" aria-expanded="false">
                    Activity
                </a>
            </li>
            <li class="nav-item">
                <a href="#messages-2" class="nav-link" data-toggle="tab" aria-expanded="true">
                    Settings
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active show" id="home-2">
                <div class="timeline-2">
                    <div class="time-item">
                        <div class="item-info">
                            <small class="text-muted">5 minutes ago</small>
                            <p><strong><a href="#" class="text-info">John Doe</a></strong> Uploaded a photo <strong>"DSC000586.jpg"</strong></p>
                        </div>
                    </div>

                    <div class="time-item">
                        <div class="item-info">
                            <small class="text-muted">30 minutes ago</small>
                            <p><a href="" class="text-info">Lorem</a> commented your post.</p>
                            <p><em>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam laoreet tellus ut tincidunt euismod. "</em></p>
                        </div>
                    </div>

                    <div class="time-item">
                        <div class="item-info">
                            <small class="text-muted">59 minutes ago</small>
                            <p><a href="" class="text-info">Jessi</a> attended a meeting with<a href="#" class="text-success">John Doe</a>.</p>
                            <p><em>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam laoreet tellus ut tincidunt euismod. "</em></p>
                        </div>
                    </div>

                    <div class="time-item">
                        <div class="item-info">
                            <small class="text-muted">1 hour ago</small>
                            <p><strong><a href="#" class="text-info">John Doe</a></strong>Uploaded 2 new photos</p>
                        </div>
                    </div>

                    <div class="time-item">
                        <div class="item-info">
                            <small class="text-muted">3 hours ago</small>
                            <p><a href="" class="text-info">Lorem</a> commented your post.</p>
                            <p><em>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam laoreet tellus ut tincidunt euismod. "</em></p>
                        </div>
                    </div>

                    <div class="time-item">
                        <div class="item-info">
                            <small class="text-muted">5 hours ago</small>
                            <p><a href="" class="text-info">Jessi</a> attended a meeting with<a href="#" class="text-success">John Doe</a>.</p>
                            <p><em>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam laoreet tellus ut tincidunt euismod. "</em></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="messages-2">

                <div class="row m-t-10">
                    <div class="col-8">
                        <h5 class="m-0">Notifications</h5>
                        <p class="text-muted m-b-0"><small>Do you need them?</small></p>
                    </div>
                    <div class="col-4 text-right">
                        <input type="checkbox" checked data-plugin="switchery" data-color="#1bb99a" data-size="small"/>
                    </div>
                </div>

                <div class="row m-t-10">
                    <div class="col-8">
                        <h5 class="m-0">API Access</h5>
                        <p class="m-b-0 text-muted"><small>Enable/Disable access</small></p>
                    </div>
                    <div class="col-4 text-right">
                        <input type="checkbox" checked data-plugin="switchery" data-color="#1bb99a" data-size="small"/>
                    </div>
                </div>

                <div class="row m-t-10">
                    <div class="col-8">
                        <h5 class="m-0">Auto Updates</h5>
                        <p class="m-b-0 text-muted"><small>Keep up to date</small></p>
                    </div>
                    <div class="col-4 text-right">
                        <input type="checkbox" checked data-plugin="switchery" data-color="#1bb99a" data-size="small"/>
                    </div>
                </div>

                <div class="row m-t-10">
                    <div class="col-8">
                        <h5 class="m-0">Online Status</h5>
                        <p class="m-b-0 text-muted"><small>Show your status to all</small></p>
                    </div>
                    <div class="col-4 text-right">
                        <input type="checkbox" checked data-plugin="switchery" data-color="#1bb99a" data-size="small"/>
                    </div>
                </div>

            </div>
        </div>
    </div> <!-- end nicescroll -->
</div>
<!-- /Right-bar -->


<footer class="footer text-right">
    <?php echo date("Y"); ?> &copy; Self Management App
</footer>


</div>
<!-- END wrapper -->


<script>
    var resizefunc = [];
</script>

<!-- jQuery  -->
<script src="/assets/js/jquery.min.js"></script>
<script src="/assets/js/popper.min.js"></script><!-- Popper for Bootstrap -->
<script src="/assets/js/bootstrap.min.js"></script>
<script src="/assets/js/detect.js"></script>
<script src="/assets/js/fastclick.js"></script>
<script src="/assets/js/jquery.blockUI.js"></script>
<script src="/assets/js/waves.js"></script>
<script src="/assets/js/jquery.nicescroll.js"></script>
<script src="/assets/js/jquery.scrollTo.min.js"></script>
<script src="/assets/js/jquery.slimscroll.js"></script>
<script src="/assets/plugins/switchery/switchery.min.js"></script>

<!-- Counter Up  -->
<script src="/assets/plugins/waypoints/lib/jquery.waypoints.min.js"></script>
<script src="/assets/plugins/counterup/jquery.counterup.min.js"></script>

<!-- Knob -->
<script src="/assets/plugins/jquery-knob/jquery.knob.js"></script>
<!-- multi select -->
<script type="text/javascript" src="/assets/plugins/multiselect/js/jquery.multi-select.js"></script>
<!-- Peity chart js -->
<script src="/assets/plugins/peity/jquery.peity.min.js"></script>

<!--Form Wizard-->
<script src="/assets/js/jquery.smartWizard.js"></script>

    <script type="text/javascript">
        $(document).ready(function(){
			//DAILY AUDIT
			// Then hide the second div
			$(".hide-content").hide();

			// Then add a click handlers to the buttons
			$(".hide-button").click(function() {
				var num = $(this).attr("href");
				
				//We eliminate the '#'
				var newStr = num.replace(/#/g, "");
				
				//alert('content-' + newStr);
				
				$(".content-" + newStr).show();
				//$(".content-" + newStr).hide();
			});
	
            // Smart Wizard 2
            $('#smartwizard').smartWizard({
                    selected: 0,
                    theme: 'dots',
                    transitionEffect:'fade',
                    showStepURLhash: false
            });

        });
    </script>

<!-- Required datatable js -->
<script src="/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<!-- Buttons examples -->
<script src="/assets/plugins/datatables/dataTables.buttons.min.js"></script>
<script src="/assets/plugins/datatables/buttons.bootstrap4.min.js"></script>
<script src="/assets/plugins/datatables/jszip.min.js"></script>
<script src="/assets/plugins/datatables/pdfmake.min.js"></script>
<script src="/assets/plugins/datatables/vfs_fonts.js"></script>
<script src="/assets/plugins/datatables/buttons.html5.min.js"></script>
<script src="/assets/plugins/datatables/buttons.print.min.js"></script>

<!-- Key Tables -->
<script src="/assets/plugins/datatables/dataTables.keyTable.min.js"></script>

<!-- Responsive examples -->
<script src="/assets/plugins/datatables/dataTables.responsive.min.js"></script>
<script src="/assets/plugins/datatables/responsive.bootstrap4.min.js"></script>

<!-- Selection table -->
<script src="/assets/plugins/datatables/dataTables.select.min.js"></script>


<!-- App js -->
<script src="/assets/js/jquery.core.js"></script>
<script src="/assets/js/jquery.app.js"></script>

<!--Morris Chart-->
<script src="/assets/plugins/morris/morris.min.js"></script>
<script src="/assets/plugins/raphael/raphael-min.js"></script>


<!-- Page specific js -->
<script src="/assets/pages/jquery.dashboard.js"></script>

<!-- following js will activate the menu in left side bar based on url -->
<script type="text/javascript">
    // === following js will activate the menu in left side bar based on url ====
    $(document).ready(function() {
        $("#sidebar-menu a").each(function() {
        var pageUrl = window.location.href.split(/[?#]/)[0];
        if (this.href == pageUrl) {
                $(this).addClass("active");
                $(this).parent().addClass("active"); // add active to li of the current link
                $(this).parent().parent().prev().addClass("active"); // add active class to an anchor
                $(this).parent().parent().prev().click(); // click the item to make it drop
            }
        });
    });
</script>

    <!-- Validation js (Parsleyjs) -->
    <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('form').parsley();
        });
    </script>

    <script src="/assets/plugins/moment/moment.js"></script>
    <script src="/assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="/assets/plugins/mjolnic-bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
    <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="/assets/plugins/clockpicker/bootstrap-clockpicker.js"></script>
    <script src="/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>

    <script src="/assets/pages/jquery.form-pickers.init.js"></script>

	<!-- Table sorter -->
	<script src="/assets/js/Sortable.js"></script>

	<!-- Text Editor -->
	<script src="/assets/js/tinymce/tinymce.min.js"></script>


	<script type="text/javascript">

		//DO not validate the numeric forms, because if you do, they do not allow for decimals numbers
		jQuery('input[type="number"]').attr('data-parsley-excluded', 'true');
		
		//Code for disabling Notifications
		jQuery( ".close-smdc" ).click(function() {

			jQuery.post('/includes/act/action.smalldesc.php', {   small_desc : $(this).attr("small-desc") },
				function(returnedData){
					//DEBUG
					//alert(returnedData);
			});
		});

		//Textarea Editor
		tinymce.init({
			selector: '#text-editor',
			plugins: "link, lists",
			mobile: {
				theme: 'mobile'
			},
		});

		//Sortable Rows
		var el = document.getElementById('sortable_rows');
		var sortable = new Sortable(el, {

			// Element dragging ended
			onEnd: function (/**Event*/evt) {

				var itemEl = evt.item;  // dragged HTMLElement

				//DEBUG
					//alert('MOVEMENT END: ' + itemEl.nextElementSibling.getAttribute('pmid'));
					//console.log(itemEl);

				evt.to;    // target list
				evt.from;  // previous list
				evt.oldIndex;  // element's old index within old parent
				evt.newIndex;  // element's new index within new parent

				jQuery.post('/includes/act/action.sorting.php', {   database : itemEl.getAttribute('ds'),
																	moved_item_id : itemEl.getAttribute('pmid'),
																	prev_item_id : itemEl.nextElementSibling.getAttribute('pmid'),

																	moved_item_timestamp : itemEl.getAttribute('plid'),
																	prev_item_timestamp : itemEl.nextElementSibling.getAttribute('plid') },
					function(returnedData){
						//DEBUG
						//alert(returnedData);
				});
			},

		});

	</script>

	<!-- Tables -->
	<script type="text/javascript">
			 $(document).ready(function() {


					 // Responsive Datatable
					 $('#responsive-datatable').dataTable( {
						 "lengthMenu": [4],
						  "order": [],
						"pageLength": 24,
						 "searching": false,
				     "bLengthChange": false,
				     "bFilter": false,
				     "bInfo": false,
						"bPaginate": false,
			     } );

			 } );

	 </script>


	<!-- Toastr js -->
	<script src="/assets/plugins/toastr/toastr.min.js"></script>

	<script type="text/javascript">
		toastr.options = {
			"closeButton": false,
			"debug": false,
			"newestOnTop": false,
			"progressBar": false,
			"positionClass": "toast-top-right",
			"preventDuplicates": false,
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "5000",

			<?php
				if(!empty($alert['non-hide'])) {
			?>
				"timeOut": "0",
				"extendedTimeOut": "0",
			<?php
				}
				else {
			?>
				"timeOut": "5000",
				"extendedTimeOut": "1000",
			<?php
				}
			?>

			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		}

		<?php
			if ( !empty($alert) ) {
				echo "toastr.{$alert['type']}('{$alert['content']}')";
			}
			elseif ( !empty($general_alert) ) {
						echo "toastr.{$general_alert['type']}('{$general_alert['content']}')";
					}

		?>
	</script>

</body>
</html>
