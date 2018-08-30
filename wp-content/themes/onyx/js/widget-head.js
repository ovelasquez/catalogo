jQuery(document).ready(function($){

	/*Agregar widget a menu responsive*/
$("#menu-menu-principal-1").append(
'<li class= "li-widget"><a href="http://www.epa.biz/ve/tiendas/" target="_blank" class="text-icon"><img class= "icon-menu" src="http://www.epa.biz:81/wp-content/uploads/2018/05/triangulo-azul1.svg"/><span class="sub-tiendas">Ver Tiendas</span></a></li><li class= "li-widget"><a class="text-icon bold-text disable"><span>Horario de 8am a 7pm</span></a></li><li class= "li-widget"><a class="text-icon black-text"><span class= "text-number disable">05003728253</span></a></li><li class= "icons-menu"><a href="https://www.facebook.com/FerreteriaEPAVE/" target="_blank" class="social-icon icon-space"><img src="http://www.epa.biz:81/wp-content/uploads/2018/05/face.svg"></a></li><li class= "icons-menu"><a href="https://twitter.com/ferreteriaepave?lang=es" target="_blank" class="social-icon"><img src="http://www.epa.biz:81/wp-content/uploads/2018/05/boton-de-logo-del-twitter.svg"></a></li><li class= "icons-menu"><a href="https://www.instagram.com/ferreteriaepave/?hl=es-la" class="social-icon" target="_blank"><img src="http://www.epa.biz:81/wp-content/uploads/2018/05/instagram-blue.svg"></a></li>');
 /*Carousel catalogo*/

//Buscador 
 $( "#searchsubmit" ).replaceWith( "<button type='submit' id='searchsubmit' class='btn btn-default'><i class='fa fa-search'></i></button>" );

  $( "#s" ).attr( "placeholder", "¿Qué busca?" );
  console.log("hecho");

  $( "#s" ).addClass("input-search");

//Buscador responsive

$( "#menu-menu-principal-1" ).find( "#searchsubmit" ).replaceWith( "<button type='submit' id='searchsubmit' class='btn btn-default'><i class='fa fa-search'></i></button>" );
$( "#menu-menu-principal-1" ).find( "#s" ).attr( "placeholder", "¿Qué busca?" );
$( "#menu-menu-principal-1" ).find( "#s" ).addClass("input-search");
  
	$("#catalogo").flexisel({
		visibleItems: 4,
		itemsToScroll: 4,
		animationSpeed: 500,
		infinite: true,
		navigationTargetSelector: null,
		autoPlay: {
			enable: true,
			interval: 5000,
			pauseOnHover: true
		},
		responsiveBreakpoints: {
			portrait: {
				changePoint: 480,
				visibleItems: 1,
				itemsToScroll: 1
			},
			landscape: {
				changePoint: 640,
				visibleItems: 1,
				itemsToScroll: 1
			},
			tablet: {
				changePoint: 800,
				visibleItems: 2,
				itemsToScroll: 2
			}
		},
		start: function(){
         $('.#catalogo').show(); 
    	}

    });


    $(".btn-cat").click(function(){

    	var slug=  $(this).data('slug'); 

    	var index = $(".item."+slug).index();

    	 // Activate Carousel

    $("#myCarousel").carousel("pause");

    $('#myCarousel').carousel({
    interval: false
	}); 

    $("#myCarousel").carousel(index);
    

    });

   /*Slider HOME*/ 
   $(".mybuttoms button").click(function(){
		$(".mybuttoms button").removeClass("active");
		$(this).addClass("active");
		$('#myCarousel iframe').each(function(){
		  this.contentWindow.postMessage('{"event":"command","func":"stopVideo","args":""}', '*') /*Youtube cancel when tabs clicked*/
		});
	});
   /*Set the first slide of a slider*/
   //$('.mybuttoms .tema_mes').click();

   /*hamburguer fix*/
   $(".mobile_menu_button").click(function(){
   		if($(".mobile_menu").hasClass("visible")){
			$(".mobile_menu").removeClass("visible");
   		}
   		else{
   			$(".mobile_menu").addClass("visible");
   		}
   		
   });
});

