// On document ready:
		jQuery(function($){
			$(function(){
					// Instantiate MixItUp:
					var mixitContainer = $('#Container').mixItUp({
						load: {
								filter: '.slide-1'
							}
						});
					var typeEffect = ['scale','translateX','translateY','translateZ','rotateX','rotateY','rotateZ','stagger'];
					var animation = "";
					var effectIndex = "";
					var configurationObject = null;
					var start = 1;
					var max = $('.mix').length;
					var i=start;
					var intervalId = setInterval(function() {
						i++;
						if(i>max) i=start;
						
						effectIndex = Math.floor((Math.random() * typeEffect.length));
						animation = 'fade ' + typeEffect[effectIndex] + '(-20deg)';
						console.log(animation);

						configurationObject = {
													animation: {
														effects:animation
													}
												};
						mixitContainer.mixItUp('setOptions', configurationObject);
						mixitContainer.mixItUp('filter', '.slide-'+i);
						

					}, 3000);
				});
			});