var hide_them = [
	// edit
	'#wpbody-content > .wrap > h2 > a.add-new-h2',
	'#postbox-container-2',
	'#tagsdiv-events_tags',
	// list
	'table .column-author',
	'table .column-comments',
	'table .column-date',
	// profile
	'#your-profile > h3:first',
	'#your-profile > table.form-table:first'
];
jQuery(document).ready(function(e){
	jQuery(hide_them.join()).addClass('hidden');
});
