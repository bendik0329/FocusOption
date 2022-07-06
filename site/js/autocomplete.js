/**
 * jQuery UI autocomplete.js
 * 
 * Remark: 
 * Commented snippets were commented by Anatoly.
 * DO NOT uncomment.
 */
function loadProfiles(affId) {
    var firstIndex  = affId.indexOf("[");
    var secondIndex = affId.indexOf("]");
    affId           = affId.substring(firstIndex + 1 , secondIndex);
    
    $("[name=profile_id]").html("");
    $.get("ajax/loadProfilesByAffiliateId.php", { affiliate_id: affId }, function(res) {
        try {
            res = JSON.parse(res);
            
            if (res["success"]) {
                for (var i = 0; i < res["success"].length; i++) {
				
                    $("<option>")
                        .attr("value", res["success"][i]["value"])
                        .text(res["success"][i]["text"])
                        .appendTo($("[name=profile_id]"));
				
                }
            }
        }
        catch (error) {
            console.log(error);
        }
    });
}

(function( $ ) {
    $.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
          
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },
      
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
        value  = selected.val() ? selected.text() : '';
        a = this;
		
        this.input = $( "<input>" )
            .appendTo( this.wrapper )
            .val(value)
            .attr('name', 'affiliate_id')
            .click(function() {
                this.focus(); 
                this.select();
            })
			.keypress(function(e){
				txtVal = this.value;
				
				if(event.which == 13 && txtVal.indexOf("[") == -1 ){
					e.preventDefault();
						
						   valueLowerCase = this.value;
						  valid = false;
						  console.log(this)
						  b=this;
						a.element.children( "option" ).each(function() {
							txtVal = $( this ).text().toLowerCase();
						  if ( $( this ).val() === valueLowerCase || txtVal.indexOf(valueLowerCase) !== -1) {
							this.selected = valid = true;
							b.value = $(this).text();
							return false;
						  }
						});
				}
			})
            .change(function() {
                loadProfiles($(this).val());
            })
            .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
            .autocomplete({
                delay: 0,
                minLength: 0,
                source: $.proxy( this, "_source" ),
                select: function (event, ui) {
                    loadProfiles(ui.item.value);
                }
            })
      },
      
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;
          
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Show All Items" )
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
            
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          return;
        }
        
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " didn't match any item" );
        
        this.element.val( "" );
        this.input.autocomplete( "instance" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      },
	  autocomplete : function(value) {
			this.element.val(value);
			var selected = this.element.children(":selected"),
                value = selected.val() ? selected.text() : "";
        this.input.val(value);
		}
    });
  })( jQuery );
 

$(function() {
    $( "#combobox" ).combobox();
    $( "#toggle" ).click(function() {
        $( "#combobox" ).toggle();
    });
});
