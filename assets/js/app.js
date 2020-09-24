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
$('.form-check-label').addClass('btn').addClass('btn-danger')
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
		$('.form-check-label').removeClass('btn-danger').html("<i>L'image sera supprimée à l'enregistrement</i><br><small class='btn btn-info' style='padding:0.2em; margin-top:1em;'>Annuler</small>");
	}
	else
	{
		$('#card_imageFile_delete').prop('checked', false)
		$('.form-check-label').addClass('btn-danger').text("Supprimer l'image");
	}
})


// Redirection au clic sur une carte
$('.indexCard').on('click', function(){
	var path = $(this).data('path');
	window.location.assign(path);
})


// Border bottom qui coulisse
var underlined = $('.focus-border').parent()

function fn1(){
	var parentWidth = $(this).css('width')
	$(' .focus-border', this).animate({
		width: parentWidth
	})
}
function fn2(){
	$(' .focus-border', this).animate({
		width: 0
	})
}
$(underlined).on("mouseenter", fn1).on("mouseleave", fn2)
