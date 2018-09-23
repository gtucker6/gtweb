(function($, Drupal, window, document, undefined) {
  'use strict';

  Drupal.tigrisCharts = Drupal.tigrisCharts || {charts: []};
  Drupal.behaviors.initializeCharts = {
    attach: function (context, settings) {
      if (typeof settings.tigrisChart !== 'undefined') {
        let provider = settings.tigrisChart.provider;
        let id = settings.tigrisChart.id;
        let type = settings.tigrisChart.type;
        $(context).find('.charts-' + provider).once('initializeCharts').each(function () {
          let options = JSON.parse($(this).attr(provider + '-options'));
          let data = $(this).data('chart');
          let chart = new TigrisChart(provider, id, type);
          let widthUnits = settings.tigrisChart.widthUnits;
          let heightUnits = settings.tigrisChart.heightUnits;

          chart.load();
          chart.setChartData(data);
          chart.setOptions(options);
          $(this).bind("DOMNodeInserted", function () {
            let $outerChart = $(this.firstElementChild);
            let $innerChart = $(this).find('[dir="ltr"]');

            $innerChart.addClass('charts-' + settings.tigrisChart.provider + '-inner');
            $outerChart.addClass('charts-' + settings.tigrisChart.provider + '-outer');
            $(this).find('svg > rect').attr('fill', 'transparent');
          });
        });
        }
      }
  };
  let TigrisChart = function(provider, id, chartType) {
    this.provider = provider;
    this.id = id;
    this.type = chartType;
    this.load = function() {
      if (this.provider === 'google') {
        google.charts.load('current', {packages: ['corechart', 'imagebarchart']});
        google.charts.setOnLoadCallback(this.drawChart);
      }
    }.bind(this);
    this.setOptions = function (options) {
      this.options = options;
    };
    this.setOption = function (key, value) {
      this.options[key] = value;
    };
    this.getOptions = function () {
      return this.options;
    };
    this.setChartData = function (data) {
      this.data = data;
    };
    this.getData = function () {
      let newData = [];
      let data = this.data;
      for (let datum in data) {
        if (data.hasOwnProperty(datum)) {
          if (datum === '0') {
            let dataStructure = ['Skill', 'Confidence Level', { role: "style" }];
            newData.push(dataStructure);
          }
          else {
            let dataStructure = [data[datum][0], data[datum][1], '#B9DCE2'];
            newData.push(dataStructure);
          }
        }
      }
      return newData;
    }.bind(this);
    this.drawChart = function () {
      // Define the chart to be drawn.
      var data = google.visualization.arrayToDataTable(this.getData());
      if (chartType === 'bar') {
        var chart = new google.visualization.BarChart(document.getElementById(id));
        chart.draw(data, this.getOptions());
      }
    }.bind(this);
  };
})(jQuery, Drupal, this, this.document);