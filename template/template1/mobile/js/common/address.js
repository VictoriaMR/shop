$ready(function(){
	console.log('ready')
	$click('.address-edit-btn', function(){
		var config = this.getAttribute('data-config');
		initAddress(config);
	});
	function initAddress(config) {

	}
	$click('#address-container .remove', function(){
		this.parentNode.querySelector('input').value = '';
	});
});