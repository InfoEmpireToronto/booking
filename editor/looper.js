if(typeof(__) == 'undefined')
{
	__ = {
		forEach: function(ar, callback){
			if(typeof(ar) == 'array')
			{
				for (var i = 0; i != ar.length; i++)
				{
					callback.call(ar[i], ar[i], i, ar);
				}
			}
			else if(typeof(ar) == 'object')
			{
				for (var prop in ar)
				{
					if (ar.hasOwnProperty(prop))
					{
						callback.call(ar[prop], ar[prop], prop, ar);
					}
				}
			}
		},
		filter: function(ar, property, value)
		{
			let out = [];
			this.forEach(ar, function(){
				if(this[property] == value)
				{
					out.push(this);
				}
			});
			return out;
		}
	}
}