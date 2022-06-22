
class izoAutocomplete
{
    constructor(element, options)
    {
        let defaults = {
            choices: [],
            onInput: false,            
            onInputDelay: 500,            
        }

        this.options = $.extend({}, defaults, options)
        this.element = $(element);
        this.matchedChoices = [];
        this.currentFocus = -1;
        
        let that = this
        
        //use custom onInput funciton 
        if(this.options.onInput)
        {
            this.element.on('input',function(){
                that.options.onInput.call(that,that,$(this).val());
            })            
        }
        else
        {
            //dfatul on input action
            this.element.on('input',function(){
                
                let val = $(this).val()
                
                that.matchedChoices = [];
                
                if(val.length)
                that.options.choices.forEach((value)=>{
                    if(value.toUpperCase().includes(val.toUpperCase()))
                    {
                        that.matchedChoices.push(value)
                    }
                })
                
                that.popup()  
                
                that.currentFocus = -1
            })                        
        }
             
        //handle keydown 
        this.element.on('keydown',function(e){
            var x = document.getElementById($(this).attr('id')+ "izo-autocomplete-list");
            
            if (x)
            {
                x = x.getElementsByTagName("div");
            }            
            
            if (e.keyCode == 40)
            {
                /*If the arrow DOWN key is pressed,
                 increase the currentFocus variable:*/
                that.currentFocus++;
                /*and and make the current item more visible:*/
                that.addActive(x);
            }
            else if (e.keyCode == 38)
            { //up
                /*If the arrow UP key is pressed,
                 decrease the currentFocus variable:*/
                that.currentFocus--;
                /*and and make the current item more visible:*/
                that.addActive(x);
            }
            else if (e.keyCode == 13)
            {
                /*If the ENTER key is pressed, prevent the form from being submitted,*/
                e.preventDefault();
                if (that.currentFocus > -1)
                {
                    /*and simulate a click on the "active" item:*/
                    if (x) x[that.currentFocus].click();
                }
            }
        })                        
    } 
    addActive(x)
    {
        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        this.removeActive(x);
        
        if (this.currentFocus >= x.length) this.currentFocus = 0;
        if (this.currentFocus < 0) currentFocus = (x.length - 1);
        
        /*add class "autocomplete-active":*/
        x[this.currentFocus].classList.add("izo-autocomplete-active");
    }
    removeActive(x)
    {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++)
        {
            x[i].classList.remove("izo-autocomplete-active");
        }
    }
    static closeAllLists(element)
    {
        //console.log($(element).attr('id'))
        
        if(element)
        {
           $('.izo-autocomplete-items').not('#'+$(element).attr('id')+'izo-autocomplete-list').remove()  
        }
        else
        {
            $('.izo-autocomplete-items').remove() 
        }
        return false;
    }
    popup()
    {
        izoAutocomplete.closeAllLists()
        
        if(this.matchedChoices.length==0) return false;
        
        //console.log(this.matchedChoices)
        
        /*create a DIV element that will contain the items (values):*/
        let a = document.createElement("DIV");
        a.setAttribute("id", this.element.attr('id') + "izo-autocomplete-list");
        a.setAttribute("class", "izo-autocomplete-items");
        a.setAttribute("style", "width: "+this.element.outerWidth()+"px");
        
        let that = this
        
        this.matchedChoices.forEach(value=>{
            /*create a DIV element for each matching element:*/
            let b = document.createElement("DIV");                        
            b.innerHTML = value
            /*insert a input field that will hold the current array item's value:*/
            b.innerHTML += "<input type='hidden' value='" + value + "'>";
            /*execute a function when someone clicks on the item value (DIV element):*/
            b.addEventListener("click", function (e)
            {
                /*insert the value for the autocomplete text field:*/
                that.element.val(this.getElementsByTagName("input")[0].value);
                /*close the list of autocompleted values,
                 (or any other open lists of autocompleted values:*/
                izoAutocomplete.closeAllLists();
            });
            
            a.appendChild(b);            
        })
                
        this.element.after(a)
        
    }
}

$(function () {
    $.fn.izoAutocomplete = function (options)
    {
        this.each(function () {  
            $(this).attr('autocomplete', 'none')
            new izoAutocomplete(this, options)
        })
    }
    
    /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        izoAutocomplete.closeAllLists(e.target);
    });
})