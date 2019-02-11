	$(function() { 
	var wrapper = $('#wrapper');	
	
    $.ajax({
     method: "POST",
     url: "https://api.graphcms.com/simple/v1/TempAPI",
     contentType: "application/json",
     headers: {
        Authorization: "bearer -----------"
    },
    data: JSON.stringify({
        query: "query  { allProducts { id title } }"
    })
}).done(function(data) {
    
	for( var key in data ) {
		 
		 for (var i = 0; i<data[key].allProducts.length; i++)
		 {
		     var container = $('<div id="data" class="container"></div>');
			 wrapper.append(container);
			 container.append('<input type=checkbox name="id" value=' + data[key].allProducts[i].id + '>' + data[key].allProducts[i].title  );
            	 
		 }   
	
    }
	
  });
	})