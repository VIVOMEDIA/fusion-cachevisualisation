function initialize() {
  $('div[data-vivomedia-cache-visualisation]').hover(
    function() {
      var container = $( this );
      $('> .data-container', container).show();
    },
    function(){
      var container = $( this );
      $('> .data-container', container).hide();
  });


  $('div[data-vivomedia-cache-visualisation]').each(function () {
    var wrapper = $(this);
    var dataContainer = $('<div class="data-container"></div>');
    wrapper.prepend(dataContainer);

    var data = wrapper.data('vivomedia-cache-visualisation');
    $.each(data, function(key, value){
      dataContainer.append($('<label />').text(key));
      if ($.isArray(value)) {
        var list = $('<ul />');
        $.each(value, function(arrayKey, arrayValue) {
          list.append($('<li />').text(arrayValue));
        });
        dataContainer.append(list);
      } else {
        if (value == null) {
          value = 'null';
        }
        dataContainer.append($('<p />').text(value));
      }
    });
  });
}

if (window.$) {
  $(document).ready(initialize);
} else {
  var script = document.createElement('script');
  script.src = 'https://code.jquery.com/jquery-3.4.1.min.js';
  script.setAttribute('integrity', 'sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=');
  script.setAttribute('crossorigin', 'anonymous');
  script.addEventListener('load', initialize);
  document.head.appendChild(script);
}
