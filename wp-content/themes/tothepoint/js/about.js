jQuery(document).ready(function($) {

    Theme.About.Init();
});

var Theme = Theme || {};
(function($) {    
    Theme.About = {
		Init: function()
		{
			// init moment range (https://github.com/gf3/moment-range#browser)
			window['moment-range'].extendMoment(moment);
			
			$post = $('#main').find('.page .entry-content');
			
			this.createLanguagesGraph($post);
		},
		
		createLanguagesGraph: function($container)
		{		    
			var $canvas = $('<canvas>').addClass('graph langs');
			$container.append($canvas);

			var graphData = this.getLanguageGraphData();
			var datasets = [];
			var datasetSize = 0;
			var totalPeriod;
			for ( var langKey in graphData)
            {
                datasets.push({data: graphData[langKey].days, 
                               label: graphData[langKey].language.find('.name').html(),
                               backgroundColor: graphData[langKey].color});
                datasetSize = graphData[langKey].days.length;
                
                // find the total period
                if(totalPeriod === undefined)
                {
                    totalPeriod = graphData[langKey].period;
                }
                else
                {
                    totalPeriod = moment.range(moment.min(totalPeriod.start, graphData[langKey].period.start), moment.max(totalPeriod.end, graphData[langKey].period.end));
                }
            }
			
			// create the labels. Need datasetSize number of points in time between start-end of the period (inclusive)
			var valuesNeeded = datasetSize - 2;
			var rangeMs = totalPeriod.end.toDate().getTime() - totalPeriod.start.toDate().getTime();
			var stepMs = rangeMs / (valuesNeeded + 1);
			var labels = [];
			labels.push(totalPeriod.start.toDate());
			for(var i = 0; i < valuesNeeded; i++)
		    {
			    labels.push(new Date(labels[0].getTime() + i * stepMs));
		    }
			labels.push(totalPeriod.end.toDate());
						
			var chart = new Chart($canvas, {
			    type: 'line',
			    data:{
			        labels: labels,
			        datasets: datasets
			      },
			    options: {
			        scales: {
			            xAxes: [{
			                type: 'time',
			                time: {
			                    min: labels[0],
			                    max: labels[labels.length - 1 ],
			                    unit: 'year'
                              },
			                ticks:{
			                    autoSkip: true,
			                    maxRotation : 0,
			                    autoSkipPadding: 35
			                }
			                }],
			            yAxes: [{
			                stacked: true,
			                display: true,
			                ticks: {
			                    min: 0,
			                    max: 100
			                }
			            }]
			        },
			        legend: { display: true },
			        //tooltips: { enabled: false },
			        elements: { point: { radius: 0 /* disable points */} }
			    }
			});
		
		},
		
		getLanguageGraphData: function()
		{
			var languageSlug = "language";
			
			// map from project:component:langs to lang:project
			// langSlug => {projectid, language, project, project-wise weight}
			var langData = {};
			$('.projects .project .component').has('.component-type[data-slug="'+languageSlug+'"]').each(function(i, el){
				// gather data
				var $lang = $(el);
				var $project = $lang.closest('.project');
				var weight = $lang.data('weight');
				var startDate = moment($project.find('time.start').attr('datetime')).add(0, 'h');
				var endDate = moment($project.find('time.end').attr('datetime')).add(23, 'h');
				var langColor = $lang.data('color');
			
				// add lang
				var langId = $lang.data('id');
				if(!(langId in langData))
				{
					langData[langId] = [];
				}
				langData[langId].push({'projectId': $project.data('id'), 'language': $lang, 'project': $project, 'period': moment.range(startDate, endDate), 'weight': weight, 'color': langColor});
			});
			
			// create the data for the datasets
			// each language exists in projects of varying date ranges. Create the full range for each language and set the combined (cross project) weight per date.
			// create the arrays. resolution: 1 day
			var min, max;
			for (var langKey in langData) 
			{
				// projects of the lang
				for(var projKey in langData[langKey])
				{
					var data = langData[langKey][projKey];
					if(min === undefined)
					{
						min = data.period.start;
						max = data.period.end;
					}
					else
					{
						min = moment.min(min, data.period.start);
						max = moment.max(max, data.period.end);
					}
				}
			}
			
			var periodDays = Math.ceil(moment.range(min, max).diff('days', true)); // number of days rounded up bc start just midnight and end is before midnight. So diff is like 500.97 days instead of 501
			
			// create the dataset per language. Each language has an array of size periodDays and each day holds the combined weight of all projects taking place.
			var graphData = {};
			for (var langKey in langData)
			{
				var langName;
				for(var projKey in langData[langKey])
				{
					var data = langData[langKey][projKey];
					
					// init language in graph data
					if(graphData[langKey] === undefined)
					{
						graphData[langKey] = {};
						graphData[langKey].language = data.language;
						graphData[langKey].color = data.color;
						graphData[langKey].days = new Array(periodDays).fill(0);
					}
					

                    if(!('period' in graphData[langKey]))
                    {
                        graphData[langKey].period = data.period;
                        
                    }
                    else
                    {
                        graphData[langKey].period = moment.range(moment.min(graphData[langKey].period.start, data.period.start), moment.max(graphData[langKey].period.end, data.period.end));
                    }
					
					// get the index in the days array for the project start/end
					var indexStart = moment.range(min, data.period.start).diff('days');
					var indexEnd = moment.range(min, data.period.end).diff('days');
					
					// add the weight to all of them
					var weight = data.language.data('weight');
					for(var i = indexStart; i <= indexEnd; i++)
					{
						graphData[langKey].days[i] += weight;
					}
				}
			}
						
			// scale all weights between 0-100. This is to be done day by day, not for the whole thing.
			for(var i = 0; i < periodDays; i++)
			{
			    // iterate once to get the max, then another one to set percentages
			    var totalWeight = 0;
			    for(langKey in graphData)
	            {
			        totalWeight += graphData[langKey].days[i];
	            }
			    
			    for(langKey in graphData)
		        {
			        graphData[langKey].days[i] = graphData[langKey].days[i] * 100 / totalWeight;			        
		        }
			}
			
			// reduce resolution. average day weight
			var mergeDays = 180; // n days in one
			for(langKey in graphData)
            {
			    var days = graphData[langKey].days;
			    var newDays = new Array(Math.ceil(days.length/mergeDays)).fill(0);
			    			    
                for(var i = 0; i < days.length; i++)
                {
                    // not all sections contain the same amount of elements, as the last one might contain less. adjust the mergeDays
                    var mergeDaysForSection = mergeDays;                    
                    if(Math.floor(i / mergeDays) === newDays.length - 1)
                    {
                        mergeDaysForSection = days.length - mergeDays * Math.floor(days.length / mergeDays);
                    }
                    
                    newDays[Math.floor(i / mergeDays)] += days[i] / mergeDaysForSection;
                }
                graphData[langKey].days = newDays;
            }
			
			return graphData;
		}
    };
})( jQuery );





