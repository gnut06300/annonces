var counter=$('#annonce_images .row').length;//On crée un compteur
		$("#add_image").click(function(){
			//recupère le prototype
			var tmpl=$("#annonce_images").data("prototype");
			tmpl=tmpl.replace(/__name__/g,counter++);//g= toutes les occurences
			$("#annonce_images").append(tmpl);
					//console.log(tmpl);
			deleteBlock();
		});

		function deleteBlock(){
			$('.del_image').click(function(){

			var	id=$(this).data('block');//$this = pour ce bouton
			//console.log(id);
			$("#"+id).remove();

			});
		
		}
		deleteBlock();