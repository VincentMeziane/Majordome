/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import $ from 'jquery';
import 'bootstrap';



// Gestion du bouton "supprimer l'image - Formulaire d'édition de carte"
$('.form-check-label', '#cardEdit').addClass('btn').addClass('btn-pale')
$('.custom-file-input').on('change', function(e){
	var inputFile = e.currentTarget;
	$('~.custom-file-label',inputFile).text(inputFile.files[0].name);
})
$(' .form-check-label', '.vich-image').on('click', function(e){
	e.preventDefault();
	var checked = $('#card_imageFile_delete').prop('checked');
	if(checked === false)
	{
		$('#card_imageFile_delete').prop('checked', true)
		$('.form-check-label').removeClass('btn-pale').html("<i>L'image sera supprimée à l'enregistrement</i><br><small class='btn btn-pale' style='padding:0.2em; margin-top:1em;'>Annuler</small>");
	}
	else
	{
		$('#card_imageFile_delete').prop('checked', false)
		$('.form-check-label').addClass('btn-pale').text("Supprimer l'image");
	}
})


// Redirection au clic sur une carte
$('.indexCard').on('click', function(){
	var path = $(this).data('path');
	window.location.assign(path);
})


// Border bottom qui coulisse
var underlined = $('.focus-border').parent()
for (let i = 0; i < underlined.length; i++) {
	var element = underlined[i];
	$(element).css('position','relative');
}

function fn1(e){
	var parentWidth = $(this).css('width')
	$(' .focus-border', this).stop().animate({
		width: parentWidth
	})
}
function fn2(e){
	$(' .focus-border', this).stop().animate({
		width: 0
	})
}
$(underlined).on("mouseenter", fn1).on("mouseleave", fn2);


// Disparition automatique des alertes après 2s
$(document).ready(function () {
	if($('.alert').length >0)
	{	
		var decompte = 5;
		decomptage()
		function decomptage(){
			decompte--;
			if(decompte == 0){
				$('.alert').slideUp(1500, function(){
					$(this).remove();
				})

			}
			else{
				var rappel = setTimeout(decomptage, 1000)
			}
		}
	}

	if($('#registration_form_agreeTerms').length > 0)
	{
		var span = document.createElement('div');
		span.setAttribute('id', 'registerSpan');
		$('label', '.form-check').append(span);
	}
});

// Modification des checkboxes
$('.rememberMeLabel').on('click', function(){
	var $checked = $('#_remember_me').is(":checked");
	if($checked === false){
		$('#rememberMeSpan').css({
			'background-color': 'rgb(255, 255, 36)',
			'border': '2px white solid'
		})
	}
	else{
		$('#rememberMeSpan').css({
			'background': 'none',
			'border': '1px rgb(255, 255, 36) solid'
		})
	}
})
$('+label', '#registration_form_agreeTerms').on('click', function(){
	var $checked = $('#registration_form_agreeTerms').is(":checked");
	console.log($checked);
	if($checked === false){
		$('#registerSpan').css({
			'background-color': 'rgb(255, 255, 36)',
			'border': '2px white solid'
		})
	}
	else{
		$('#registerSpan').css({
			'background': 'none',
			'border': '1px rgb(255, 255, 36) solid'
		})
	}
})

