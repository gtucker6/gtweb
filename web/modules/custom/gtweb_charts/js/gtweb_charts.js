(function($, Drupal, window, document, undefined) {
  'use strict';

  Drupal.tigrisCharts = Drupal.tigrisCharts || {charts: []};
  Drupal.behaviors.tigrisChart = {
    attach: function (context, settings) {
      let provider = settings.tigrisChart.provider;
      let id = settings.tigrisChart.id;
      let type = settings.tigrisChart.type;
      if (typeof settings.tigrisChart !== 'undefined') {
        $(context).find('.charts-' + provider).once('tigrisChart').each(function () {
          let chart = new TigrisChart(provider, id, type);
          chart.load();
          if ($(chart.id).attr('id') === 'web_skills__block_web_skills_level_chart') {
            let dataStructure = ['Skill', 'Confidence Level ', { role: "style" }, { role: "style"}];
            let options = chart.getOptions();
            options['hAxes']['0'].format = '#\'%\'';
            chart.setOptions(options);
            chart.addChartData(0, dataStructure);
            chart.alterChartData(dataStructure, chart.getOptions()['colors']);
          }
          $(this).bind("DOMNodeInserted", function () {
            let $outerChart = $(this.firstElementChild);
            let $innerChart = $(this).find('[dir="ltr"]');
            $innerChart.addClass('charts-' + chart.provider + '-inner');
            $outerChart.addClass('charts-' + chart.provider + '-outer');
            $(this).find('svg > rect').attr('fill', 'transparent');
          });
        });
        }
      }
  };
  let TigrisChart = function (provider, id, type) {
    this.provider = provider;
    this.id = $('#' + id);
    this.type = type;
    this.options = JSON.parse($(this.id).attr(provider + '-options'));
    this.data = $(this.id).data('chart');
    this.load = function () {
      if (this.provider === 'google') {
        google.charts.load('current', {packages: ['corechart']});
        google.charts.setOnLoadCallback(this.drawChart);
      }
    }.bind(this);
    this.setOptions = function (options) {
      this.options = options;
      $(this.id).attr(provider + '-options', JSON.stringify(this.options));
    };
    this.setOption = function (key, value) {
      this.options[key] = value;
      $(this.id).attr(provider + '-options', JSON.stringify(this.options));
    };
    this.getOptions = function () {
      return this.options;
    };
    this.addChartData = function (key, value) {
      this.data[key] = value;
      $(this.id).attr('data-chart', JSON.stringify(this.data));
    };
    this.alterChartData = function (dataStructure, additionalOptions) {
      let columnCount = dataStructure.length;
      let alteredData = [];
      for (let datum in this.data) {
        if (this.data.hasOwnProperty(datum)) {
          if (datum !== '0') {
            let newData = [];
            for (let i = 0; i < columnCount; i++) {
              if (this.data[datum].hasOwnProperty(i)) {
                newData.push(this.data[datum][i]);
              }
            }
            let yLabel = this.data[0][1];
            let x = this.data[datum][0];
            let y = this.data[datum][1];
            let alteredDataPart = newData.concat(additionalOptions).concat([yLabel + ' ' + y + '%']);
            alteredData.push(alteredDataPart);
            this.addChartData(datum, alteredDataPart);
          }
        }
        if (alteredData.length > 0) {
          $(this.id).attr('data-chart', JSON.stringify(alteredData));
        }
      }
    }.bind(this);
    this.drawChart = function () {
      // Define the chart to be drawn.
      var data = google.visualization.arrayToDataTable(this.data);
      if (this.type === 'bar') {
        var chart = new google.visualization.BarChart(document.getElementById(id));
        chart.draw(data, this.getOptions());

      }
    }.bind(this);
  };
})(jQuery, Drupal, this, this.document);
