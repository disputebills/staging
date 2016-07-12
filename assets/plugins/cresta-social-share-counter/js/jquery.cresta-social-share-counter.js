(function($) {
	"use strict";
	
	$(document).ready(function() {
	
		/*-----------------------------------------------------------------------------------*/
		/*  Social Counter JS
		/*-----------------------------------------------------------------------------------*/ 		
		var $URL = crestaPermalink.thePermalink;
		var $ismorezero = crestaPermalink.themorezero;
		totalShares($URL);
		
			if ( $('#linkedin-cresta').hasClass('linkedin-cresta-share') ) {
				// Linkedin Shares Count via PHP
				var LinkedinShares = crestaShareSS.LinkedinCount;
				var linkedinvar = $('<span class="cresta-the-count" id="linkedin-count"></span>').text(ReplaceNumberWithCommas(LinkedinShares));
				if (LinkedinShares > 0 || $ismorezero == 'nomore') {
					$('.linkedin-cresta-share.float a').after(linkedinvar)
				}
				$('#total-shares').attr('data-linkedInShares', LinkedinShares)
			} else {
				$('#total-shares').attr('data-linkedInShares', 0)
			}
		
			function ReplaceNumberWithCommas(shareNumber) {
				 if (shareNumber >= 1000000000) {
					return (shareNumber / 1000000000).toFixed(1).replace(/\.0$/, '') + 'G';
				 }
				 if (shareNumber >= 1000000) {
					return (shareNumber / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
				 }
				 if (shareNumber >= 1000) {
					return (shareNumber / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
				 }
				 return shareNumber;
			}

			// Google Plus Shares Count
			function googleplusShares($URL) {
				if ( $('#googleplus-cresta').hasClass('googleplus-cresta-share') ) {
					var gplusRequest = [{
						"method":"pos.plusones.get",
						"id":"p",
						"params":{
							"nolog":true,
							"id":$URL,
							"source":"widget",
							"userId":"@viewer",
							"groupId":"@self"
							},
						"jsonrpc":"2.0",
						"key":"p",
						"apiVersion":"v1"
					}];

					$.ajax({
						url: "https://clients6.google.com/rpc?key=AIzaSyCKSbrvQasunBoV16zDH9R33D88CeLr9gQ",
						type: "POST",
						data: JSON.stringify(gplusRequest),
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						success: function(googleplusSharess) {
							var googleplusvar = $('<span class="cresta-the-count" id="googleplus-count"></span>').text(ReplaceNumberWithCommas(googleplusSharess[0].result.metadata.globalCounts.count));
							if (googleplusSharess[0].result.metadata.globalCounts.count > 0 || $ismorezero == 'nomore') {
								$('.googleplus-cresta-share.float a').after(googleplusvar)
							}
							$('#total-shares').attr('data-googleplusShares', googleplusSharess[0].result.metadata.globalCounts.count)
						}
					});
				} else {
					$('#total-shares').attr('data-googleplusShares', 0)
				}
			}
			
			// Facebook Shares Count
			function facebookShares($URL) {
				if ( $('#facebook-cresta').hasClass('facebook-cresta-share') ) {
					$.getJSON('https://graph.facebook.com/?id=' + $URL, function (fbdata) {
						var facebookvar = $('<span class="cresta-the-count" id="facebook-count"></span>').text(ReplaceNumberWithCommas(fbdata.shares || 0));
						if (fbdata.shares > 0 || $ismorezero == 'nomore') {
							$('.facebook-cresta-share.float a').after(facebookvar)
						}
						$('#total-shares').attr('data-facebookShares', fbdata.shares || 0)
					});
				} else {
					$('#total-shares').attr('data-facebookShares', 0)
				}
			}

			// Twitter Shares Count
			function twitterShares($URL) {
				if ( $('#twitter-cresta').hasClass('twitter-cresta-share') && $('#twitter-cresta').hasClass('withCount') ) {
					$.getJSON('https://public.newsharecounts.com/count.json?url=' + $URL + '&callback=?', function (twitterdata) {
						var twittervar = $('<span class="cresta-the-count" id="twitter-count"></span>').text(ReplaceNumberWithCommas(twitterdata.count));
						if (twitterdata.count > 0 || $ismorezero == 'nomore') {
							$('.twitter-cresta-share.float a').after(twittervar)
						}
						$('#total-shares').attr('data-twitterShares', twitterdata.count)
					});
				} else {
					$('#total-shares').attr('data-twitterShares', 0)
				}
			}
			/*
			// LinkedIn Shares Count
			function linkedInShares($URL) {
				if ( $('#linkedin-cresta').hasClass('linkedin-cresta-share') ) {
					$.getJSON('https://www.linkedin.com/countserv/count/share?url=' + $URL + '&callback=?', function (linkedindata) {
						var linkedinvar = $('<span class="cresta-the-count" id="linkedin-count"></span>').text(ReplaceNumberWithCommas(linkedindata.count));
						if (linkedindata.count > 0 || $ismorezero == 'nomore') {
							$('.linkedin-cresta-share.float a').after(linkedinvar)
						}
						$('#total-shares').attr('data-linkedInShares', linkedindata.count)
					});
				} else {
					$('#total-shares').attr('data-linkedInShares', 0)
				}
			}
			*/
			// Pinterest Shares Count
			function pinterestShares($URL) {
				if ( $('#pinterest-cresta').hasClass('pinterest-cresta-share') ) {
					$.getJSON('https://api.pinterest.com/v1/urls/count.json?url=' + $URL + '&callback=?', function (pindata) {
						var pinterestvar = $('<span class="cresta-the-count" id="pinterest-count"></span>').text(ReplaceNumberWithCommas(pindata.count));
						if (pindata.count > 0 || $ismorezero == 'nomore') {
							$('.pinterest-cresta-share.float a').after(pinterestvar)
						}
						$('#total-shares').attr('data-pinterestShares', pindata.count)
					});
				} else {
					$('#total-shares').attr('data-pinterestShares', 0)
				}
			}

			// Check if all JSON calls are finished or not
			function checkJSON_getSum() {
				if ($('#total-shares, #total-shares-content').attr('data-facebookShares') != undefined &&
				$('#total-shares, #total-shares-content').attr('data-linkedinShares') != undefined &&
				$('#total-shares, #total-shares-content').attr('data-pinterestShares') != undefined &&
				$('#total-shares, #total-shares-content').attr('data-twitterShares') != undefined &&
				$('#total-shares, #total-shares-content').attr('data-googleplusShares') != undefined) {

					if ( $('#facebook-cresta').hasClass('facebook-cresta-share')) {
						var fbShares = parseInt($('#total-shares').attr('data-facebookShares'));
					} else {
						var fbShares = 0;
					}
					if ( $('#twitter-cresta').hasClass('twitter-cresta-share') && $('#twitter-cresta').hasClass('withCount')) {
						var twitShares = parseInt($('#total-shares').attr('data-twitterShares'));
					} else {
						var twitShares = 0;
					}
					if ( $('#linkedin-cresta').hasClass('linkedin-cresta-share')) {
						var linkedInShares = parseInt($('#total-shares').attr('data-linkedinShares'));
					} else {
						var linkedInShares = 0;
					}
					if ( $('#pinterest-cresta').hasClass('pinterest-cresta-share')) {
						var pinterestShares = parseInt($('#total-shares').attr('data-pinterestShares'));
					} else {
						var pinterestShares = 0;
					}
					if ( $('#googleplus-cresta').hasClass('googleplus-cresta-share') ) {
						var googleplusShares = parseInt($('#total-shares').attr('data-googleplusShares'));
					} else {
						var googleplusShares = 0;
					}
					
				} else {
					setTimeout(function () {
						checkJSON_getSum();
					}, 200);
				}
					var totalShares = fbShares + linkedInShares + pinterestShares + googleplusShares + twitShares;
					$('#total-count').text( ReplaceNumberWithCommas(totalShares) || 0 )
			}

			function totalShares($URL) {
				//linkedInShares($URL);
				twitterShares($URL);
				facebookShares($URL);
				pinterestShares($URL);
				googleplusShares($URL);
				checkJSON_getSum();
			}
	});
	
})(jQuery);