/* 	delayTime is the number of frames to wait before animating the slides, the 
	actual time depends on the framerate given by setInterval in the calling 
	webpage.  For a 5 second delay it should be set to 20 if delayTime is 250.*/
var delayTime = 250;
//	timer is used to count down the wait before the next slide is rolled in.
var timer = 0;
//	positions holds the positions for each slide.
var positions = new Array();
/*	totalSlides is taken from the number of slides given in initSlideShow and 
	should represent the number of slides defined in the presentation. */
var totalSlides = 0;
/*	judder controls the number of initial outward steps for the juddering effect.
	judderNext is used to control the number of outward steps for each stage of
	the juddering animation. */
var judder = 4; judderNext = 4;
var judderOn = false;
/*	slideToShow controls which slide should be on display. */
var slideToShow = 2;
var lastThumbnail = null;
var disableNextRoll= false;
/*	slideWidth is the width of each slide in pixels. */
var slideWidth = 540;
/*	stepSize is the number of pixels each slide should move for each frame of 
	the animation. */
var stepSize = 20;
var pulling = true;

function initSlideshow(slides) {
	totalSlides = slides;
	for (i = 1; i <= totalSlides; i++) {
		positions[i] = getStyle("slide" + i, "left").replace(/([A-Za-z]+)/,"");
	}
}

function animateSlideshow() {
	if (timer < delayTime) {
		timer++;
	} else {
		if (disableNextRoll) {
			lastThumbnail.style.border = "2px solid black";
			lastThumbnail = null;
			disableNextRoll = false;
		}
		if (positions[slideToShow] < 0 || positions[slideToShow] > stepSize && pulling) {
			for (var i = 1; i <= totalSlides; i++) {
				positions[i] -= stepSize;
				if (positions[i] < -(2 * slideWidth)) {
					var j = (i - 1 > 0) ? i - 1 : totalSlides;
					positions[i] = positions[j] + slideWidth;
				}
				getElement("slide"+i).style.left=positions[i]+"px";
			}
		} else {
			if (judderOn) {
				if (judderNext > 0) {
					if (judder > 0) {
						for (i = 1; i <= totalSlides; i++) {
							positions[i] += stepSize;
							getElement("slide"+i).style.left=positions[i]+"px";
						}
						judder--;
						pulling = false;
					} else {
						pulling = true;
						judderNext--;
						judder = judderNext;
					}
				} else {	
					timer = 0;
					judderNext = judder = 4;
					judderOn = false;
					slideToShow++;
					if (slideToShow > totalSlides) {
						slideToShow = 1;
					}
					if (lastThumbnail != null) {
						disableNextRoll = true;
					}
				}
			} else {
				/*	Because I want to use any step size, I need to make sure the 
					slides stay in the correct position when they finally come 
					to rest. */
				judderOn = true;
				var diff = 4 - positions[slideToShow];
				for (var i = 1; i <= totalSlides; i++) {
					positions[i] += diff;
					getElement("slide"+i).style.left=positions[i]+"px";
				}
			}
		}
	}
}

function setSlideToShow(thumbnail, slide) {
	if (lastThumbnail != thumbnail) {
		if (lastThumbnail != null) {
			lastThumbnail.style.border = "solid 2px black";
		}
		thumbnail.style.border = "2px solid white";
		disableNextRoll = false;
		lastThumbnail = thumbnail;
		slideToShow = slide;
		timer = delayTime;
	}
}
