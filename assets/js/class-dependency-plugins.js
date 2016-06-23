IMHWPB.ClassDependencyPlugins = function(configs) {
	var self = this;

	jQuery(function() {
		jQuery('div#admin_notice_dependency_plugins button.notice-dismiss').on(
				'click', function() {
					self.dismiss_notice();
					jQuery(this).closest('div.updated').slideUp('slow');
					return false;
				});
	});

	this.dismiss_notice = function() {
		var data = {
			'action' : 'boldgrid_dismiss_notice',
			'notice' : 'class-dependency-plugins'
		};

		jQuery.post(ajaxurl, data, function(response) {
		});
	};
};

new IMHWPB.ClassDependencyPlugins();
