
var table;

$(function() {
  const createdCell = function(cell) {
	var original;
  cell.setAttribute('contenteditable', true)
  cell.setAttribute('spellcheck', false)
  cell.addEventListener("focus", function(e) {
		original = e.target.textContent;
    e.target.textContent = '';
	})
  cell.addEventListener("blur", function(e) {
  //VALORE CORRENTE CELLA MODIFICATA e.target.textContent
		if (original !== e.target.textContent && e.target.textContent!=='') {
    //INDEXs CELLA CORRENTE
      var irow = table.cell(this).index().row;
      var icolumn = table.cell(this).index().columnVisible;
    //MODIFICA definitivamente CELLA con nuovo valore immesso
      table.cell(irow,icolumn).data(e.target.textContent);
    //GET VALORI CELLE
      var vsp = table.cell(irow,1).data();
      var stock = table.cell(irow,2).data();
      var sc = table.cell(irow,3).data();
      var g = table.cell(irow,4).data();
      var psic = table.cell(irow,5).data();
      var gf = table.cell(irow,6).data();
    //NOME COLONNA DELLA CELLA CLICCATA
      var nomeColonna = $('#tableProdotti th').eq($(this).index()).text();
      switch(nomeColonna){
        case 'Venduto settimana precedente':
          sc = calcolaSpazioCella(vsp,stock);
          table.cell(irow,3).data(sc);
          gf = calcolaGiacenzaFutura(psic,g,vsp,sc);
          table.cell(irow,6).data(gf);
          pf = calcolaProduzioneFutura(vsp,sc,gf);
          table.cell(irow,7).data(pf);
          break;
        case 'Stock':
          sc = calcolaSpazioCella(vsp,stock,gf);
          table.cell(irow,3).data(sc);
          pf = calcolaProduzioneFutura(vsp,sc,gf);
          table.cell(irow,7).data(pf);
          break;
        case 'Giacenza':
          gf = calcolaGiacenzaFutura(psic,g,vsp,sc);
          table.cell(irow,6).data(gf);
          pf = calcolaProduzioneFutura(vsp,sc,gf);
          table.cell(irow,7).data(pf);
          break;  
        case 'Produzione settimana in corso':
          gf = calcolaGiacenzaFutura(psic,g,vsp,sc);
          table.cell(irow,6).data(gf);
          pf = calcolaProduzioneFutura(vsp,sc,gf);
          table.cell(irow,7).data(pf);
          break;
        default:
          console.log('Sorry, we are out of ${nomeColonna}.');
      }
    }
    else{
      const row = table.row(e.target.parentElement)
      row.invalidate()
    }
  })
} 
  table = $('#tableProdotti').DataTable( {
        ajax: "prodotti.json",
        dom: 'Bfrtip',
        buttons: [
          {
                text: 'Delete',
                action: function ( e, dt, node, config ) {
                  var indexes = table
                  .rows()
                  .indexes()
                  .filter( function ( value, index ) {
                    //alert(table.row(value).data())
                    if((table.row(value).data()[7])==0){
                      return true;
                    }
                    else{
                      return false;
                    }
                  });
                  deleteRows(indexes);
                }
          },
          {     
                extend: 'csv',
                text: 'Csv',
          },
          {     
                extend: 'excel',
                text: 'Excel',
          },
          {     
                extend: 'pdf',
                text: 'Pdf',
                exportOptions: {
                  columns: [0,7]
                }
          }
        ],
          initComplete: function () {
            var btns = $('.dt-button');
            //btns.addClass('btn btn-success btn-sm');
            btns.css('background', 'white');
        },
        columnDefs: [
          { 
            targets: [1,2,4,5,7], //'_all'
            createdCell: createdCell
          }
        ]
      } );

} );

function calcolaSpazioCella(vsp,stock) {
  let mvsp = parseFloat(vsp)*parseFloat(1.7);
  let sc =  parseFloat(mvsp)+parseFloat(stock);
  return Math.ceil(sc);
}

function calcolaGiacenzaFutura(psic,g,vsp,sc) {
  let gf = parseFloat(psic)+parseFloat(g)-parseFloat(vsp);
  return Math.ceil(gf); 
}

function calcolaProduzioneFutura(vsp,sc,gf) {
  //alert('')
  let pf = parseFloat(0);
  if((parseFloat(gf)/parseFloat(vsp))>1.5){
    return Math.ceil(pf);
  }
  else{
    pf=parseFloat(sc)-parseFloat(gf);
    return Math.ceil(pf);
  }
}

function deleteRows(indexes) {
  /*table.rows().every(function(index){
    alert(this.data()+index);
    table.rows(index).remove().draw();
});*/
  /*var row = table.rows().data();
  row.each(function (value, index) {
  alert('For index '+index+', data value is '+value);
    if(value[7]===0){
      table.rows(index).remove().draw();
    }
  });*/
  table.rows(indexes).remove().draw();
}
