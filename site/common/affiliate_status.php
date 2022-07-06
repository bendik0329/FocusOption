<?php 

$set->content .='<style>
					.wrapper-dropdown-3 {
    /* Size and position */
    position: relative;
    width: 278px;
    margin: 0px;
    padding: 6px;

    /* Styles */
    background: #fff;
    border-radius: 7px;
    border: 1px solid rgba(0,0,0,0.15);
    box-shadow: 0 1px 1px rgba(50,50,50,0.1);
    cursor: pointer;
    outline: none;

    /* Font settings */
    /*font-weight: bold;
    color: #8AA8BD;*/
}

.wrapper-dropdown-3:after {
    content: "";
    width: 0;
    height: 0;
    position: absolute;
    right: 15px;
    top: 50%;
    margin-top: -3px;
    border-width: 6px 6px 0 6px;
    border-style: solid;
    border-color: #000 transparent;
}


.wrapper-dropdown-3 .dropdown_new {
  /* Size & position */
    position: absolute;
    /* top: 140%; */
	top: 100%;
    left: 0;
    right: 0;
	margin-left:0px;
	padding-left:0px;
    /* Styles */
    background: white;
    border-radius: inherit;
    border: 1px solid rgba(0,0,0,0.17);
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
    font-weight: normal;
    -webkit-transition: all 0.5s ease-in;
    -moz-transition: all 0.5s ease-in;
    -ms-transition: all 0.5s ease-in;
    -o-transition: all 0.5s ease-in;
    transition: all 0.5s ease-in;
    list-style: none;

    /* Hiding */
    opacity: 0;
    pointer-events: none;
}

.wrapper-dropdown-3 .dropdown_new:after {
    content: "";
    width: 0;
    height: 0;
    position: absolute;
    bottom: 100%;
    right: 15px;
    border-width: 0 6px 6px 6px;
    border-style: solid;
    border-color: #fff transparent;    
}

.wrapper-dropdown-3 .dropdown_new:before {
    content: "";
    width: 0;
    height: 0;
    position: absolute;
    bottom: 100%;
    right: 13px;
    border-width: 0 8px 8px 8px;
    border-style: solid;
    border-color: rgba(0,0,0,0.1) transparent;    
}

.wrapper-dropdown-3 .dropdown_new li a {
    display: block;
    padding: 10px;
    text-decoration: none;
    /*color: #8aa8bd;*/
    border-bottom: 1px solid #e6e8ea;
    box-shadow: inset 0 1px 0 rgba(255,255,255,1);
    -webkit-transition: all 0.3s ease-out;
    -moz-transition: all 0.3s ease-out;
    -ms-transition: all 0.3s ease-out;
    -o-transition: all 0.3s ease-out;
    transition: all 0.3s ease-out;
}

.wrapper-dropdown-3 .dropdown_new li i {
    float: right;
    color: inherit;
}

.wrapper-dropdown-3 .dropdown_new li:first-of-type a {
    border-radius: 7px 7px 0 0;
}

.wrapper-dropdown-3 .dropdown_new li:last-of-type a {
    border: none;
    border-radius: 0 0 7px 7px;
}

/* Hover state */

.wrapper-dropdown-3 .dropdown_new li:hover a {
    background: #f3f8f8;
}

/* Active state */

.wrapper-dropdown-3.active .dropdown_new {
    opacity: 1;
    pointer-events: auto;
}

/* No CSS3 support */

.no-opacity       .wrapper-dropdown-3 .dropdown_new,
.no-pointerevents .wrapper-dropdown-3 .dropdown_new {
    display: none;
    opacity: 1; /* If opacity support but no pointer-events support */
    pointer-events: auto; /* If pointer-events support but no pointer-events support */
}

.no-opacity       .wrapper-dropdown-3.active .dropdown_new,
.no-pointerevents .wrapper-dropdown-3.active .dropdown_new {
    display: block;
}

					</style>';
					
					
					$set->content .='<script type="text/javascript">
			
			function DropDown(el) {
				this.dd = el;
				this.placeholder = this.dd.children(\'span\');
				this.opts = this.dd.find(\'ul.dropdown_new > li\');
				this.val = "";
				this.index = -1;
				this.initEvents();
			}
			DropDown.prototype = {
				initEvents : function() {
					var obj = this;

					obj.dd.on(\'click\', function(event){
						$(this).toggleClass(\'active\');
						return false;
					});

					obj.opts.on(\'click\',function(){
						var opt = $(this);
						//setting the value of hidden varible to save in database
						valid = $(this).find("a").data("valid");
						$("#db_valid").val(valid);
						
						obj.val = opt.text();
						obj.index = opt.index();
						obj.placeholder.text(obj.val);
					});
				},
				getValue : function() {
					return this.val;
				},
				getIndex : function() {
					return this.index;
				}
			}

			$(function() {

				var dd = new DropDown( $(\'#dd\') );

				$(document).click(function() {
					// all dropdowns
					$(\'.wrapper-dropdown-3\').removeClass(\'active\');
				});

			});

		</script>';
		