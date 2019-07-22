(function() {
    function IDGenerator() {
    
        this.length = 4;
        this.timestamp = +new Date;
        
        var _getRandomInt = function( min, max ) {
           return Math.floor( Math.random() * ( max - min + 1 ) ) + min;
        }
        
        this.generate = function() {
            var ts = this.timestamp.toString();
            var parts = ts.split( "" ).reverse();
            var id = "";
            
            for( var i = 0; i < this.length; ++i ) {
                var index = _getRandomInt( 0, parts.length - 1 );
                id += parts[index];	 
            }
            console.log('id -->', id);
            return id;
        }
    }
    
    document.addEventListener( "DOMContentLoaded", function() {
        var btn = document.querySelector( "#generate" ),
            output = document.querySelector( "#output" );
        btn.addEventListener( "click", function() {
            var generator = new IDGenerator();
            output.innerHTML = generator.generate();
        }, false); 
        
    });
    
    
})();
