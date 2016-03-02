Handlebars.registerHelper('toLowerCase', function(str) {
	return str.toLowerCase();
});

Handlebars.registerHelper('json', function(str) {
	return JSON.stringify(str);
});

Handlebars.registerHelper('if_eq', function(a, b, opts) {
	if (a == b)
		return opts.fn(this);
	else
		return opts.inverse(this);
});

Handlebars.registerHelper('if_recommended_image_size', function(a, opts) {
	/**
	 * ************************************************************************
	 * Configure parameters.
	 * ************************************************************************
	 */

	// Default params.
	var low = 450;
	var high = 900;
	var recommended_dimensions = '';
	var recommended_width = '';

	// Get the referer.
	self.baseAdmin = new IMHWPB.BaseAdmin();
	var ref = self.baseAdmin.GetURLParameter('ref');

	// If we're editing a page/post:
	if ('dashboard-post' == ref) {
		var low = 450;
		var high = 900;
	}

	// If we're in the customizer:
	if ('dashboard-customizer') {
		// Is the WordPress theme recommending dimensions?
		// Example: Suggested image dimensions: 1600 × 230
		var $instructions = jQuery('.instructions', window.parent.document)
				.last().html();
		if (typeof $instructions != 'undefined' && $instructions.length) {
			recommended_dimensions = $instructions.split(':');
			// Note, that's not an 'x' below, it's an '×'.
			recommended_width = recommended_dimensions[1].split('×');
			recommended_width = parseInt(recommended_width[0].trim());

			low = recommended_width - 500;
			high = recommended_width + 500;
		}
	}

	/**
	 * ************************************************************************
	 * Logic, determine if this is a recommended image size.
	 * ************************************************************************
	 */
	if (a >= low && a <= high)
		return opts.fn(this);
	else
		return opts.inverse(this);
});

Handlebars.registerHelper('objCount', function(obj) {
	// return str.toLowerCase();
	return Object.keys(obj).length;
});

Handlebars.registerHelper("getValueAtKey", function(object, key) {
	return object[key];
});

Handlebars.registerHelper("getValueAtKeyKey", function(object, key1, key2) {
	return object[key1][key2];
});

Handlebars.registerHelper("multiply", function(value, multiplier) {
	return parseInt(value) * parseInt(multiplier);
});

// http://www.levihackwith.com/creating-new-conditionals-in-handlebars/
// Usage: {{#ifCond var1 '==' var2}}
Handlebars.registerHelper('ifCond', function(v1, operator, v2, options) {
	switch (operator) {
	case '==':
		return (v1 == v2) ? options.fn(this) : options.inverse(this);
	case '===':
		return (v1 === v2) ? options.fn(this) : options.inverse(this);
	case '<':
		return (v1 < v2) ? options.fn(this) : options.inverse(this);
	case '<=':
		return (v1 <= v2) ? options.fn(this) : options.inverse(this);
	case '>':
		return (v1 > v2) ? options.fn(this) : options.inverse(this);
	case '>=':
		return (v1 >= v2) ? options.fn(this) : options.inverse(this);
	case '&&':
		return (v1 && v2) ? options.fn(this) : options.inverse(this);
	case '||':
		return (v1 || v2) ? options.fn(this) : options.inverse(this);
	default:
		return options.inverse(this);
	}
});

// Determine if a variable is set and not null.
// Only supports strings at this time.
Handlebars.registerHelper('isSetAndNotNull', function(a, options) {
	var mytype = typeof a;

	switch (mytype) {
	case 'string':
		if ('' != a.trim()) {
			return options.fn(this);
		} else {
			return options.inverse(this);
		}
		break;
	default:
		// Return false by default.
		return options.inverse(this);
	}
});