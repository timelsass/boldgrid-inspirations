/**
 * BoldGrid Feedback.
 */

// Import IMHWPB.
var IMHWPB = IMHWPB || {};

/**
 * BoldGridFeedback class.
 *
 * @since 1.1
 */
IMHWPB.BoldGridFeedback = function( configs ) {
	// Declare vars.
	var self = this;

	// Initialize self.diagnosticData.
	self.diagnosticData = '';

	// Initialize self.submitStatus.
	self.submitStatus = '';

	// Use jQuery to check events and modify the form content.
	jQuery( function() {
		// Define a context selector for id "feedback-notice-1-1".
		$feedbackNotice11 = jQuery( '#feedback-notice-1-1' );

		// Define a context selector for id "feedback-type".
		$feedbackNotice11Type = $feedbackNotice11.find( '#feedback-type' );

		// Define a context selector for id "feedback-contact-checkbox".
		$feedbackNotice11ContactCheckbox = $feedbackNotice11.find( '#feedback-contact-checkbox' );

		// Define a context selector for id "feedback-submit" (the submit
		// button).
		$feedbackSubmit = $feedbackNotice11.find( '#feedback-submit' );

		// When the id "feedback-type" selection value changes, then modify form
		// content.
		$feedbackNotice11Type.change( self.toggle_type );

		// When the id "feedback-contact-checkbox" checkbox is checked, display
		// "feedback-email-address".
		$feedbackNotice11ContactCheckbox.change( self.toggle_feedback_email );

		// Handle when the feedback form submit button is clicked.
		$feedbackSubmit.on( 'click', self.submit_feedback_form );
	} );

	/**
	 * Toggle the display of the feedback email address.
	 *
	 * @since 1.1
	 */
	self.toggle_feedback_email = function() {
		// Define a context selector.
		$feedbackEmailAddress = $feedbackNotice11.find( '#feedback-email-address' );

		// Toggle the display of the email address line.
		if ( $feedbackNotice11ContactCheckbox.is( ':checked' ) ) {
			$feedbackEmailAddress.css( 'display', 'block' );
		} else {
			$feedbackEmailAddress.css( 'display', 'none' );
		}
	}

	/**
	 * Toggle the display of the diagnostic report.
	 *
	 * @since 1.1
	 */
	self.toggle_type = function() {
		// Define a context selector for id "feedback-comment-area".
		$feedbackComment = $feedbackNotice11.find( '#feedback-comment-area' );

		// Define a context selector for id "feedback-diagnostic-report".
		$feedbackDiagnosticReport = $feedbackNotice11.find( '#feedback-diagnostic-report' );

		// Define a context selector for id "feedback-diagnostic-report".
		$feedbackDiagnosticReportText = $feedbackNotice11.find( '#feedback-diagnostic-text' );

		// Modify content based on selected feedback type.
		if ( '' == $feedbackNotice11Type.val() ) {
			// Hide the comment area.
			$feedbackComment.css( 'display', 'none' );

			// Hide the diagnostic report area.
			$feedbackDiagnosticReport.css( 'display', 'none' );

			// Disable the diagnostic report text area.
			$feedbackDiagnosticReportText.prop( 'disabled', 'disabled' );

			// Hide the submit button.
			$feedbackSubmit.css( 'display', 'none' );

			// Disable the submit button.
			$feedbackSubmit.prop( 'disabled', 'disabled' );
		} else {
			// Show the comment area.
			$feedbackComment.css( 'display', 'block' );

			// Enable the submit button.
			$feedbackSubmit.prop( 'disabled', false );

			// Show the submit button.
			$feedbackSubmit.css( 'display', 'block' );

			// Toggle the display of the diagnostic report area.
			if ( 'Bug report' == $feedbackNotice11Type.val() ) {
				// Enable the diagnostic report text area.
				$feedbackDiagnosticReportText.prop( 'disabled', false );

				// Show the diagnostic report area.
				$feedbackDiagnosticReport.css( 'display', 'block' );

				// Populate diagnostic data, if needed.
				if ( '' == $feedbackDiagnosticReportText.val() ) {
					// Retrieve the data.
					self.diagnosticData = self.populateDiagnosticData();

					// Update the form.
					$feedbackDiagnosticReportText.val( self.diagnosticData );
				}
			} else {
				// Hide the diagnostic report area.
				$feedbackDiagnosticReport.css( 'display', 'none' );

				// Disable the diagnostic report text area.
				$feedbackDiagnosticReportText.prop( 'disabled', 'disabled' );
			}
		}
	}

	/**
	 * Set self.diagnosticData.
	 *
	 * @since 1.1
	 *
	 * @param string
	 *            diagnosticData Diagnostic information in standard text.
	 * @return null
	 */
	self.setDiagnosticData = function( diagnosticData ) {
		self.diagnosticData = diagnosticData;

		return;
	}

	/**
	 * Populate diagnostic data.
	 *
	 * This function can be called independently to retrieve diagnostic data
	 * (text).
	 *
	 * @since 1.1
	 *
	 * @return string Diagnostic information in standard text.
	 */
	self.populateDiagnosticData = function() {
		// Initialize diagnosticData.
		var data, diagnosticData;

		// Check if data was already retreived.
		if ( self.diagnosticData.length > 0 ) {
			return self.diagnosticData;
		}

		// Retrieve the data via AJAX.

		// Generate the data array.
		data = {
			'action' : 'boldgrid_feedback_diagnostic_data'
		};

		// Make the call.
		jQuery.ajax( {
		    url : ajaxurl,
		    data : data,
		    async : false,
		    type : 'post',
		    dataType : 'text',
		    success : function( output ) {
			    diagnosticData = output;
		    }
		} );

		// Set self.diagnosticData.
		self.diagnosticData = diagnosticData;

		// Return the data.
		return self.diagnosticData;
	}

	/**
	 * Submit feedback form.
	 *
	 * @since 1.1
	 */
	self.submit_feedback_form = function() {
		// Define a var object for the form data.
		var formData = {}, markup;

		// Define a context selector for id "feedback-notice-1-1-intro".
		$feedbackHeader = $feedbackNotice11.find( '#feedback-notice-1-1-intro' );

		// Define a context selector for id "feedback-notice-1-1".
		$feedbackContent = $feedbackNotice11.find( '#feedback-notice-1-1-content' );

		// Define a context selector for id "feedback-notice-1-1".
		$feedbackForm = $feedbackContent.find( '#boldgrid-feedback-form' );

		// Get the form data.
		formData.feedbackType = $feedbackForm.find( '#feedback-type' ).val();

		formData.comment = $feedbackForm.find( '#feedback-comment' ).val();

		formData.contactMe = $feedbackForm.find( '#feedback-contact-checkbox' );

		if ( formData.contactMe.is( ':checked' ) ) {
			formData.emailAddress = $feedbackForm.find( '#feedback-email' ).val();

			formData.contactMe = 'Yes';
		} else {
			formData.contactMe = 'No';
		}

		if ( 'Bug report' == formData.feedbackType ) {
			formData.diagnosticReport = $feedbackForm.find( '#feedback-diagnostic-text' ).val();
		}

		// Add feedback.

		// Generate the data array.
		data = {
		    'action' : 'boldgrid_feedback_submit',
		    'form_data' : formData
		};

		// Make the call.
		jQuery.ajax( {
		    url : ajaxurl,
		    data : data,
		    async : false,
		    type : 'post',
		    dataType : 'text',
		    success : function( reponse ) {
			    submitStatus = reponse;
		    }
		} );

		// Set self.submitStatus.
		self.submitStatus = submitStatus;

		// Check response.
		if ( 'Success' == submitStatus ) {
			// Replace the form with a success message.
			markup = "<h2>Thanks for the feedback</h2>\n"
			    + "<p>The BoldGrid team wants you to know that we are listening and every bit of </p>\n"
			    + "<p>feedback helps us improve out tool.</p>";
		} else {
			// Replace the form with an error message.
			markup = "<h2>BoldGrid Feedback Request</h2>\n"
			    + "<p>There was an error processing your request.  Please try again.</p>";

			// Add the error class to the admin notice.
			$feedbackNotice11.addClass( 'error' );
		}

		// Empty the notice area.
		$feedbackContent.empty();

		// Insert markup in the notice.
		$feedbackHeader.html( markup );

		// Return false so the page does not reload.
		return false;
	}
};

new IMHWPB.BoldGridFeedback();
