document.addEventListener("DOMContentLoaded", function () {
  var cacheVisualisations = document.querySelectorAll('div[data-vivomedia-cache-visualisation]');
  [].forEach.call(cacheVisualisations, function(cacheVisualisationItem) {

      var data = JSON.parse( cacheVisualisationItem.dataset.vivomediaCacheVisualisation );

      var dataHtml = '<dl>';
      for (var key in data) {
          dataHtml += '<dt>' + key + '</dt>';
          dataHtml += '<dd>' + JSON.stringify(data[key]) + '</dd>';
      };
      dataHtml += '</dl>';

      // create data container
      var dataContainer = document.createElement("div");
      dataContainer.classList.add('data-container');
      dataContainer.innerHTML = dataHtml
      dataContainer.style.display = 'none'

      cacheVisualisationItem.appendChild(dataContainer);

      cacheVisualisationItem.onmouseover = function() {
          dataContainer.style.display = 'block';
      }

      cacheVisualisationItem.onmouseout = function(){
          dataContainer.style.display = 'none';
      }

  });
});
