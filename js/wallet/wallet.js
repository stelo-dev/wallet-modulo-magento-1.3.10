/**
 * Created by Rodrigo Ribeiro on 25/11/14.
 */


LoadScript = function (src, callback) {
				var s, r, t;
				var callback = (typeof(callback)=='function')? callback : function(){};
				r = false;
				s = document.createElement('script');
				s.type = 'text/javascript';
				s.src = src;
				s.async = true;
				s.defer = true;
				s.onload = s.onreadystatechange = function () {					
					if (!r && (!this.readyState || this.readyState == 'complete' || this.readyState == 'loaded') ) {
						r = true;                						
						callback.call(this);
					}
				};
				try {
					t = document.getElementsByTagName('script')[0];
					t.parent.insertBefore(s, t);
				} catch (e) {
					t = document.getElementsByTagName('head')[0];
					t.appendChild(s);           
				}
			}
                        
                        