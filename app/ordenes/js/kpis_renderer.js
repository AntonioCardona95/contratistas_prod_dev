/* KPIS renderer: builds minimal HTML for KPIs and renders charts from JSON data
   Compatible with Chart.js v2.x
*/
(function(window, $){
  'use strict';

  // number_format helper (same behavior as PHP version used)
  function number_format(number){
    if(number === null || number === undefined) return '0';
    var n = Math.round(number);
    return n.toString().replace(/\B(?=(?:\d{3})+(?!\d))/g, ',');
  }

  // Chart factory reused from KPIS.php approach (v2 compatible)
  function createBarChartEl(el, labels, dataArr, bgArr, borderArr){
    if(!el) return null;
    var ctx = el.getContext('2d');
    try{
      return new Chart(ctx, {
        type: 'bar',
        data: { labels: labels, datasets: [{ label: 'Registros', backgroundColor: bgArr, borderColor: borderArr, data: dataArr }] },
        options: {
          maintainAspectRatio: false,
          layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
          scales: { xAxes:[{ gridLines:{display:false,drawBorder:false}, ticks:{maxTicksLimit:6}}], yAxes:[{ ticks:{min:0,maxTicksLimit:5,padding:10, callback:function(v){return number_format(v);} }, gridLines:{color:'rgb(234,236,244)',drawBorder:false}}] },
          legend:{ display:false },
          tooltips:{ callbacks:{ label:function(t, d){ return 'Registros: ' + number_format(t.yLabel); } } },
          animation: { duration: 600 }
        }
      });
    }catch(e){ console.warn('createBarChartEl error', e); return null; }
  }

  function drawBarValues(chart){
    if(!chart) return;
    var ctx = chart.ctx;
    ctx.save();
    ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
    ctx.font = '600 12px "Nunito", Roboto, Arial, sans-serif';
    chart.data.datasets.forEach(function(dataset, i){
      var meta = chart.getDatasetMeta(i);
      meta.data.forEach(function(bar, index){
        var value = dataset.data[index];
        if(typeof value === 'number'){
          var x = (bar.x !== undefined) ? bar.x : (bar._model && bar._model.x) ? bar._model.x : 0;
          var y = (bar.y !== undefined) ? bar.y : (bar._model && bar._model.y) ? bar._model.y : 0;
          var chartBottom = chart.chartArea ? chart.chartArea.bottom : (chart.scales && chart.scales['y'] ? chart.scales['y'].bottom : 0);
          var space = chartBottom - y;
          var display = Math.round(value);
          var displayText = number_format(display);
          if(space > 24){ ctx.fillStyle = '#ffffff'; ctx.fillText(displayText, x, y + 12); }
          else { ctx.fillStyle = '#212529'; ctx.fillText(displayText, x, y - 6); }
        }
      });
    });
    ctx.restore();
  }

  // colors palette (same as PHP)
  var palette = ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#5a5c69','#fd7e14'];

  // Public render function
  window.renderKpisFromJson = function(targetSelector, data){
    // targetSelector: container where we will inject HTML (e.g. '#LoadKPIS')
    var $target = $(targetSelector);
    if(!$target.length) return;

    // Build top cards HTML minimal
    var meta = data.meta || {};
    var cardsHtml = '<div class="row">'
      + '<div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-success shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ÓRDENES REGISTRADAS TAC</div><div class="h5 mb-0 font-weight-bold text-gray-800">'+number_format(meta.TotalTac || 0)+'</div></div><div class="col-auto"><i class="fas fa-tools fa-2x text-gray-300"></i></div></div></div></div></div>'
      + '<div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-success shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ÓRDENES REGISTRADAS COORDIAPP</div><div class="h5 mb-0 font-weight-bold text-gray-800">'+number_format(meta.TotalCompletas || 0)+'</div></div><div class="col-auto"><i class="fas fa-tools fa-2x text-gray-300"></i></div></div></div></div></div>'
      + '<div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-primary shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ÓRDENES INCOMPLETAS COORDIAPP</div><div class="h5 mb-0 font-weight-bold text-gray-800">'+number_format(meta.TotalIncompletas || 0)+'</div></div><div class="col-auto"><i class="fas fa-tools fa-2x text-gray-300"></i></div></div></div></div></div>'
      + '<div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-danger shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ÓRDENES COORDIAPP PROMEDIO POR DÍA</div><div class="h5 mb-0 font-weight-bold text-gray-800">'+(Math.round((meta.Promedio||0)*100)/100)+'</div></div><div class="col-auto"><i class="fas fa-tools fa-2x text-gray-300"></i></div></div></div></div></div>'
      + '</div>';

    // build chart containers (divs + canvases)
    var chartsHtml = '';
    chartsHtml += '<div class="row">'
      + '<div class="col-xl-6 col-lg-6"><div class="card shadow mb-8"><div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"><h6 class="m-0 font-weight-bold text-primary">PRODUCCIÓN DIVISIÓN TAC</h6></div><div class="card-body"><div class="chart-area"><canvas id="myBarChartTac"></canvas></div></div></div></div>'
      + '<div class="col-xl-6 col-lg-6"><div class="card shadow mb-8"><div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"><h6 class="m-0 font-weight-bold text-primary">PRODUCCIÓN DIVISIÓN COORDIAPP</h6></div><div class="card-body"><div class="chart-area"><canvas id="myBarChartCoordiapp"></canvas></div></div></div></div>'
      + '</div>';

    // Areas
    chartsHtml += '<div class="row">'
      + '<div class="col-xl-6 col-lg-6"><div class="card shadow mb-8"><div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"><h6 class="m-0 font-weight-bold text-primary">PRODUCCIÓN ÁREAS TAC</h6></div><div class="card-body"><div class="chart-area"><canvas id="myBarChartTacAreas"></canvas></div></div></div></div>'
      + '<div class="col-xl-6 col-lg-6"><div class="card shadow mb-8"><div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"><h6 class="m-0 font-weight-bold text-primary">PRODUCCIÓN ÁREAS COORDIAPP</h6></div><div class="card-body"><div class="chart-area"><canvas id="myBarChartCoordiappAreas"></canvas></div></div></div></div>'
      + '</div>';

    // COPES
    chartsHtml += '<div class="row">'
      + '<div class="col-xl-6 col-lg-6"><div class="card shadow mb-8"><div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"><h6 class="m-0 font-weight-bold text-primary">PRODUCCIÓN COPES TAC</h6></div><div class="card-body"><div class="chart-area"><canvas id="myBarChartTacCopes"></canvas></div></div></div></div>'
      + '<div class="col-xl-6 col-lg-6"><div class="card shadow mb-8"><div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"><h6 class="m-0 font-weight-bold text-primary">PRODUCCIÓN COPES COORDIAPP</h6></div><div class="card-body"><div class="chart-area"><canvas id="myBarChartCoordiappCopes"></canvas></div></div></div></div>'
      + '</div>';

    // Technicians area chart container
    chartsHtml += '<div class="row"><div class="col-xl-12 col-lg-12"><div class="card shadow mb-4"><div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"><h6 class="m-0 font-weight-bold text-primary">TÉCNICOS MÁS PRODUCTIVOS COORDIAPP</h6></div><div class="card-body"><div class="chart-area"><canvas id="myAreaChart"></canvas></div></div></div></div></div>';

    // Inject into the target
    $target.html(cardsHtml + chartsHtml);

    // prepare arrays and create charts (immediate)
    // helper to destroy existing chart instances by canvas id
    function destroyChartById(canvasId){
      if(typeof Chart === 'undefined' || !Chart.instances) return;
      try{
        Object.keys(Chart.instances).forEach(function(k){
          var c = Chart.instances[k];
          if(c && c.canvas && c.canvas.id === canvasId){
            c.destroy();
            delete Chart.instances[k];
          }
        });
      }catch(e){ console.warn('destroyChartById error', e); }
    }
    // helper to generate colors
    function genColors(len){ var bg = [], br = []; for(var i=0;i<len;i++){ var c = palette[i % palette.length]; bg.push(c); br.push(c); } return {bg:bg, br:br}; }

    // DIVISIONES TAC
    var labelsDivTac = (data.divisionesTac||[]).map(function(r){ return r.Division || ''; });
    var dataDivTac = (data.divisionesTac||[]).map(function(r){ return parseFloat(r.TOTAL||0); });
    var cDivTac = genColors(dataDivTac.length);
    // If the TAC endpoint failed or there is no data, show a small placeholder instead of creating a heavy chart
    if ((data.failed && data.failed.tac) || dataDivTac.length === 0) {
      var tacContainer = document.getElementById('myBarChartTac');
      if(tacContainer && tacContainer.parentNode){
        tacContainer.parentNode.innerHTML = '<div class="p-4 text-center text-muted">No hay datos disponibles para PRODUCCIÓN DIVISIÓN TAC</div>';
      }
    } else {
      var chDivTac = createBarChartEl(document.getElementById('myBarChartTac'), labelsDivTac, dataDivTac, cDivTac.bg, cDivTac.br);
      if(chDivTac) { chDivTac.options.animation = chDivTac.options.animation || {}; var orig = chDivTac.options.animation.onComplete; chDivTac.options.animation.onComplete = function(){ if(orig) orig.apply(this, arguments); drawBarValues(chDivTac); }; chDivTac.update(); }
    }

    // DIVISIONES COORDI
    var labelsDivCoord = (data.divisionesCoordi||[]).map(function(r){ return r.Division || ''; });
    var dataDivCoord = (data.divisionesCoordi||[]).map(function(r){ return parseFloat(r.TOTAL||0); });
    var cDivCoord = genColors(dataDivCoord.length);
    // COORDIAPP divisions
    if ((data.failed && data.failed.coordi) || dataDivCoord.length === 0) {
      var coordContainer = document.getElementById('myBarChartCoordiapp');
      if(coordContainer && coordContainer.parentNode){ coordContainer.parentNode.innerHTML = '<div class="p-4 text-center text-muted">No hay datos disponibles para PRODUCCIÓN DIVISIÓN COORDIAPP</div>'; }
    } else {
      var chDivCoord = createBarChartEl(document.getElementById('myBarChartCoordiapp'), labelsDivCoord, dataDivCoord, cDivCoord.bg, cDivCoord.br);
      if(chDivCoord){ chDivCoord.options.animation = chDivCoord.options.animation || {}; var orig2 = chDivCoord.options.animation.onComplete; chDivCoord.options.animation.onComplete = function(){ if(orig2) orig2.apply(this, arguments); drawBarValues(chDivCoord); }; chDivCoord.update(); }
    }

    // AREAS TAC
    var labelsAreasTac = (data.areasTac||[]).map(function(r){ return r.Area || ''; });
    var dataAreasTac = (data.areasTac||[]).map(function(r){ return parseFloat(r.TOTAL||0); });
    var cAreasTac = genColors(dataAreasTac.length);
    // AREAS TAC
    if ((data.failed && data.failed.tac) || dataAreasTac.length === 0) {
      var areasTacContainer = document.getElementById('myBarChartTacAreas');
      if(areasTacContainer && areasTacContainer.parentNode){ areasTacContainer.parentNode.innerHTML = '<div class="p-4 text-center text-muted">No hay datos disponibles para PRODUCCIÓN ÁREAS TAC</div>'; }
    } else {
      var chAreasTac = createBarChartEl(document.getElementById('myBarChartTacAreas'), labelsAreasTac, dataAreasTac, cAreasTac.bg, cAreasTac.br);
      if(chAreasTac){ chAreasTac.options.animation = chAreasTac.options.animation || {}; var orig3 = chAreasTac.options.animation.onComplete; chAreasTac.options.animation.onComplete = function(){ if(orig3) orig3.apply(this, arguments); drawBarValues(chAreasTac); }; chAreasTac.update(); }
    }

    // AREAS COORDI
    var labelsAreasCoord = (data.areasCoordi||[]).map(function(r){ return r.area || r.Area || ''; });
    var dataAreasCoord = (data.areasCoordi||[]).map(function(r){ return parseFloat(r.TOTAL||0); });
    var cAreasCoord = genColors(dataAreasCoord.length);
    // AREAS COORDI
    if ((data.failed && data.failed.coordi) || dataAreasCoord.length === 0) {
      var areasCoordContainer = document.getElementById('myBarChartCoordiappAreas');
      if(areasCoordContainer && areasCoordContainer.parentNode){ areasCoordContainer.parentNode.innerHTML = '<div class="p-4 text-center text-muted">No hay datos disponibles para PRODUCCIÓN ÁREAS COORDIAPP</div>'; }
    } else {
      var chAreasCoord = createBarChartEl(document.getElementById('myBarChartCoordiappAreas'), labelsAreasCoord, dataAreasCoord, cAreasCoord.bg, cAreasCoord.br);
      if(chAreasCoord){ chAreasCoord.options.animation = chAreasCoord.options.animation || {}; var orig4 = chAreasCoord.options.animation.onComplete; chAreasCoord.options.animation.onComplete = function(){ if(orig4) orig4.apply(this, arguments); drawBarValues(chAreasCoord); }; chAreasCoord.update(); }
    }

    // COPES TAC
    var labelsCopesTac = (data.copesTac||[]).map(function(r){ return r.NOM_CT || r.NOM_CT || ''; });
    var dataCopesTac = (data.copesTac||[]).map(function(r){ return parseFloat(r.TOTAL||0); });
  console.log('COPES TAC labels:', labelsCopesTac, 'data:', dataCopesTac);
    var cCopesTac = genColors(dataCopesTac.length);
  destroyChartById('myBarChartTacCopes');
  if ((data.failed && data.failed.tac) || dataCopesTac.length === 0) {
    var copesTacContainer = document.getElementById('myBarChartTacCopes');
    if(copesTacContainer && copesTacContainer.parentNode){ copesTacContainer.parentNode.innerHTML = '<div class="p-4 text-center text-muted">No hay datos disponibles para PRODUCCIÓN COPES TAC</div>'; }
  } else {
    var chCopesTac = createBarChartEl(document.getElementById('myBarChartTacCopes'), labelsCopesTac, dataCopesTac, cCopesTac.bg, cCopesTac.br);
    if(chCopesTac){ chCopesTac.options.animation = chCopesTac.options.animation || {}; var orig5 = chCopesTac.options.animation.onComplete; chCopesTac.options.animation.onComplete = function(){ if(orig5) orig5.apply(this, arguments); drawBarValues(chCopesTac); }; chCopesTac.update(); }
  }

    // COPES COORDI
    var labelsCopesCoord = (data.copesCoordi||[]).map(function(r){ return r.COPE || r.Cope || ''; });
    var dataCopesCoord = (data.copesCoordi||[]).map(function(r){ return parseFloat(r.TOTAL||0); });
  console.log('COPES COORDI labels:', labelsCopesCoord, 'data:', dataCopesCoord);
    var cCopesCoord = genColors(dataCopesCoord.length);
  destroyChartById('myBarChartCoordiappCopes');
  if ((data.failed && data.failed.coordi) || dataCopesCoord.length === 0) {
    var copesCoordContainer = document.getElementById('myBarChartCoordiappCopes');
    if(copesCoordContainer && copesCoordContainer.parentNode){ copesCoordContainer.parentNode.innerHTML = '<div class="p-4 text-center text-muted">No hay datos disponibles para PRODUCCIÓN COPES COORDIAPP</div>'; }
  } else {
    var chCopesCoord = createBarChartEl(document.getElementById('myBarChartCoordiappCopes'), labelsCopesCoord, dataCopesCoord, cCopesCoord.bg, cCopesCoord.br);
    if(chCopesCoord){ chCopesCoord.options.animation = chCopesCoord.options.animation || {}; var orig6 = chCopesCoord.options.animation.onComplete; chCopesCoord.options.animation.onComplete = function(){ if(orig6) orig6.apply(this, arguments); drawBarValues(chCopesCoord); }; chCopesCoord.update(); }
  }

    // Tecnicos area chart (line)
    try{
      var labelsTec = (data.tecnicos||[]).map(function(r){ return r.NExpediente || ''; });
      var dataTec = (data.tecnicos||[]).map(function(r){ return parseFloat(r.TOTAL||0); });
      var ctx = document.getElementById('myAreaChart');
      if(ctx){
        if ((data.failed && data.failed.coordi) || dataTec.length === 0) {
          // replace area chart with placeholder when no data or endpoint failed
          if(ctx && ctx.parentNode) ctx.parentNode.innerHTML = '<div class="p-4 text-center text-muted">No hay datos disponibles para TÉCNICOS MÁS PRODUCTIVOS</div>';
        } else {
          new Chart(ctx.getContext('2d'), { type:'line', data: { labels: labelsTec, datasets:[{ label:'INSTALACIONES', data:dataTec, backgroundColor:'rgba(78,115,223,0.05)', borderColor:'rgba(78,115,223,1)', pointRadius:3 }] }, options:{ maintainAspectRatio:false, legend:{display:false}, scales:{ xAxes:[{gridLines:{display:false}}], yAxes:[{ticks:{callback:function(v){return number_format(v);}}}] } } });
        }
      }
    }catch(e){ console.warn('tecnicos chart error', e); }

  };

})(window, jQuery);
