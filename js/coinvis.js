var i=0, j= 0;
var topBound = 150, barWidth = 60, baselineOffset = 30, yBaseline = topBound + 30;
var getId = function(d){return d.id;};
var k = 0;

var formatRect = function(sel){
    sel.attr("x", function(d,i) { return getX(i); })
        .attr("y", function(d,i){
            console.log("i = " + i + " y = " + (topBound - Math.max(d.height, 0)) + " height = " + d.x);
            return (topBound - Math.max(d.height, 0)) + baselineOffset;
        })
        .attr("width", barWidth)
        .attr("height", function(d, i){ return Math.abs(d.height);})
        .style("fill", function(d,i){ return d.height > 0 ? '#8acc84' : '#d19b8a'});
    return sel; // should return object for chain calling.
};

var chart = d3.selectAll("svg.simpleCoin");
chart.selectAll("rect")
    .data(function(){return parseCoinDataset(this.dataset);}, getId)
    .enter().append("rect")
    .call(formatRect);

chartEnter = chart.selectAll("text")
    .data(function(){return parseCoinDataset(this.dataset);})
    .enter();

chartEnter.append("text")
    .attr("x", function(d){return getX(d.id);})
    .attr("y", function(d){
        if(d.height >= 0)
            return yBaseline + baselineOffset/2;
        else return yBaseline - baselineOffset/2;
    } )
    .attr("dx", barWidth/2)
    .attr("dy", ".36em")
    .attr('style', "font-weight: bold")
    .attr("text-anchor", "middle")
    .text(function(d){return d.tag});

chartEnter.append("text")
    .attr("x", function(d){return getX(d.id);})
    .attr("y", function(d){
        if(d.height >= 0)
            return yBaseline - d.height - baselineOffset/2;
        else
            return yBaseline - d.height + baselineOffset/2;
    } )
    .attr("dx", barWidth/2)
    .attr("dy", ".36em")
    .attr("text-anchor", "middle")
    .text(function(d){return d.change + "%"});

chart.append('line')
    .style("stroke", "#ccc")  // colour the line
    .attr("x1", 0)     // x position of the first end of the line
    .attr("y1", yBaseline)      // y position of the first end of the line
    .attr("x2", 500)     // x position of the second end of the line
    .attr("y2", yBaseline);    // y position of the second end of the line

function parseCoinDataset(set) {
    var c = JSON.parse(set.coin);
    var index = 0;
    var heights = [c.percent_change_1h, c.percent_change_24h, c.percent_change_7d];
    var bound = getBounds(heights);
    var dataset = [
        {tag: "1 hr", change: c.percent_change_1h},
        {tag: "24 hr", change: c.percent_change_24h},
        {tag: "7 days", change: c.percent_change_7d}
    ];
    return dataset.map(function (x) {
            return {id: index++, tag: x.tag, change:x.change, height: map_range(x.change, 0, bound, 0, topBound)};
        });
}
function getX(i){
    return i*barWidth*2+baselineOffset*2;
}


function map_range(value, low1, high1, low2, high2) {
    return low2 + (high2 - low2) * (value - low1) / (high1 - low1);
}

function getBounds(arr){
    var max = Math.max.apply(null, arr.map(Math.abs));
    return Math.ceil(max / 10) * 10;
}