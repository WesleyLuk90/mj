var __ = (function(key){
	var strings = {
		dora: 'Dora',
		MenzenTsumo: 'Menzen Tsumo',
		HonRouTou: 'Hon Rou Tou',
		SanAnKou: 'San An Kou',
		ToiToi: 'Toi Toi'
	};
	return strings[key] || key;
});
var HandCalculator = function(ContainerElement){
	var ImageManager = (function(){
		var cache = {};
		function loadImage(tile, name){
			if(!name){
				name = tile;
			}
			var img = new Image();
			img.src = templateDir + "/img/tiles/" + tile + ".gif";
			cache[name] = img;
		}
		for (var i = 0; i < 10; i++) {
			loadImage(i + "s");
			loadImage(i + "m");
			loadImage(i + "p");
			if( 1 <= i && i <= 7){
				loadImage(i + "z", i + "h");
			}
		};
		function getTileImage(tile){
			var img = new Image();
			img.src = cache[tile].src;
			img = $(img);
			img.data('tile', tile);
			return img;
		}
		return {
			getTileImage: getTileImage,
		}
	}());

	var TileInput = (function(){
		var positioner = $('<div class="tile-input">').appendTo(document.body);
		var container = $('<div class="well">').appendTo(positioner);
		var currentTileArea;

		var closeButton = $('<div class="close-button"><i class="icon-remove icon-white"></i></div>').appendTo(container);

		// Start edit
		var requestTileInput = function(tileArea){
			if(currentTileArea){
				currentTileArea.stopEdit();
			}
			positioner.show();
			tileArea.getElement().append(positioner);
			currentTileArea = tileArea;
		};
		var close = function(){
			if(currentTileArea){
				currentTileArea.stopEdit();
				positioner.hide();
			}
		};
		// Stop edit
		closeButton.bind('mousedown', close);

		var callbackFactory = function(tile){
			return function(){
				if(currentTileArea){
					currentTileArea.addTile(tile);
					currentTileArea.onTilesUpdate();
				}
			};
		};
		for (var i = 0; i < 10; i++) {
			var tile = ImageManager.getTileImage(i + "m");
			container.append(tile);
			$(tile).bind('mousedown', callbackFactory(i + "m"));
		};
		container.append($('<br>'));
		for (var i = 0; i < 10; i++) {
			var tile = ImageManager.getTileImage(i + "s");
			container.append(tile);
			$(tile).bind('mousedown', callbackFactory(i + "s"));
		};
		container.append($('<br>'));
		for (var i = 0; i < 10; i++) {
			var tile = ImageManager.getTileImage(i + "p");
			container.append(tile);
			$(tile).bind('mousedown', callbackFactory(i + "p"));
		};
		container.append($('<br>'));
		for (var i = 1; i < 8; i++) {
			var tile = ImageManager.getTileImage(i + "h");
			container.append(tile);
			$(tile).bind('mousedown', callbackFactory(i + "h"));
		};
		positioner.hide();

		return {
			requestTileInput: requestTileInput,
			close: close
		};
	}());

	var TileArea = (function(){
		var TileArea = function(){
			this.element = $('<div class="tile-area">');
			this.editButton = $('<i class="icon-pencil edit-button">');
			this.sortButton = $('<i class="icon-chevron-up sort-button">');
			this.element.append(this.editButton);
			this.element.append(this.sortButton);
			var self = this;
			this.editButton.bind('mousedown', function(){
				self.startEdit();
			});
			this.sortButton.bind('mousedown', function(){
				self.sortTiles();
			});
		};

		TileArea.prototype.removeTileCallbackFactory = function(img){
			var self = this;
			return function(){
				img.remove();
				self.onTilesUpdate();
			};
		};

		TileArea.prototype.sortTiles = function(){
			var tiles = this.getTiles();
			this.element.children('img').remove();
			tiles.sort(function(a,b){
				var suita = a[1] == 'h' ? 'z' : a[1];
				var suitb = b[1] == 'h' ? 'z' : b[1];

				var numbera = a[0] == '0' ? '5' : a[0];
				var numberb = b[0] == '0' ? '5' : b[0];
				if(suita < suitb){
					return -1;
				} else if(suitb < suita) {
					return 1;
				}
				if(numbera < numberb){
					return -1;
				}
				if(numberb < numbera){
					return 1;
				}
				return 0;
			});
			for (var i = 0; i < tiles.length; i++) {
				this.addTile(tiles[i]);
			};
		};

		TileArea.prototype.startEdit = function(){
			this.element.addClass('active-input');
			var self = this;
			TileInput.requestTileInput(this);
		};

		TileArea.prototype.stopEdit = function(){
			this.element.removeClass('active-input');
		};

		TileArea.prototype.getElement = function(){
			return this.element;
		};

		TileArea.prototype.addTile = function(tile){
			var tileElement = ImageManager.getTileImage(tile);
			this.element.append(tileElement);
			tileElement.bind('mousedown', this.removeTileCallbackFactory(tileElement));
		};

		TileArea.prototype.getTiles = function(){
			var elements = this.element.children('img');
			var tiles = [];
			for (var i = 0; i < elements.length; i++) {
				var element = $(elements[i]);
				tiles.push(element.data('tile'));
			};
			return tiles;
		};

		TileArea.prototype.getNormalizedTiles = function(){
			var tiles = this.getTiles();
			for (var i = 0; i < tiles.length; i++) {
				tiles[i] = tiles[i].replace(/0/g, "5");
			};
			return tiles;
		};

		TileArea.prototype.onTilesUpdate = function(){
			Calculator.updateAll();
		};

		TileArea.prototype.parseAsMelds = function(){
			var tiles = this.getNormalizedTiles().map(Riichi.Tile.createTileFromString);
			var tileNumbers = tiles.map(function(tile){
				return tile.getIndex();
			});
			var count = tiles.length;
			// Recursive parsing function, start parsing at i
			function parse(i){
				// Done, return empty array
				if(i >= count){
					return [];
				}
				if(i + 3 > count){
					// We need at least 3 tiles, otherwise fail
					return null;
				}
				var tileN1 = tileNumbers[i];
				var tile1 = tiles[i++];
				var tileN2 = tileNumbers[i];
				var tile2 = tiles[i++];
				var tileN3 = tileNumbers[i];
				var tile3 = tiles[i++];
				if(tileN1 == tileN2 - 1 && tileN2 == tileN3 - 1){
					// chi
					var rest = parse(i);
					if(rest === null){
						return null;
					}
					var chi = new Riichi.Meld([tile1, tile2, tile3]);
					return [].concat(chi, rest);
				} else if(tileN1 == tileN2 && tileN2 == tileN3){
					// A Pon
					// Check for kan
					if(i <= count && tileNumbers[i] == tileN1){
						// Possible kan
						// Try ignoring the kan
						var useKan = false;
						var rest = parse(i);
						if(rest === null){
							// Failed, try including the kan
							useKan = true;
							rest = parse(i + 1);
						}
						if(rest === null){
							// Failed path
							return null;
						}
						if(useKan){
							var meld = new Riichi.Meld([tile1, tile2, tile3, tiles[i]]);
							return [].concat(meld, rest);
						} else {
							var meld = new Riichi.Meld([tile1, tile2, tile3]);
							return [].concat(meld, rest);
						}
					} else {
						var rest = parse(i);
						if(rest === null){
							return null;
						}
						var meld = new Riichi.Meld([tile1, tile2, tile3]);
						return [].concat(meld, rest);
					}
				} else {
					// Failed parse, backtrack
					return null;
				}
			}
			return parse(0);
		}
		TileArea.prototype.reset = function(){
			this.element.find("img").remove();
		};
		TileArea.prototype.getAsKans = function(){
			var tiles = this.getTiles().map(Riichi.Tile.createTileFromString);
			if(tiles.length % 4 != 0){
				return null;
			}
			var melds = [];
			var i = 0;
			while(i < tiles.length){
				var tile1 = tiles[i++];
				var tile2 = tiles[i++];
				var tile3 = tiles[i++];
				var tile4 = tiles[i++];
				if(!tile1.equals(tile2) || !tile2.equals(tile3) || !tile3.equals(tile4)){
					return null;
				}
				var meld = new Riichi.Meld([tile1, tile2, tile3, tile4]);
				melds.push(meld);
			}
			return melds;
		};

		return TileArea;
	}());

	var InfoArea = (function(){
		var InfoArea = function(){
			this.element = $(
				'<div class="info-area">' + 
				'<h3 class="invalid-message">Invalid hand</h3>' +
				'<div>' +
					'<h3>Shanten: <span class="shanten"></span></h3>' +
					'<h3>Han</h3><table class="point-breakdown table"></table>' +
					'<h3>Waits</h3><div class="waits"></div>' +
					'<h3>Payment</h3>Normal:<div class="payment"></div>Aotenjou:<div class="payment-no-limits"></div>' +
					'<h3>Fu</h3><table class="fu-breakdown table"></table></div>' +
				'</div>' +
				'</div>'
			);
			this.shanten = this.element.find('.shanten').first();
			this.pointBreakdown = this.element.find('.point-breakdown').first();
			this.waits = this.element.find('.waits').first();
			this.payment = this.element.find('.payment').first();
			this.paymentNoLimits = this.element.find('.payment-no-limits').first();
			this.fuBreakdown = this.element.find('.fu-breakdown').first();
		};
		InfoArea.prototype.update = function(handCalculator){
			if(!handCalculator.isValid()){
				this.element.addClass('invalid-hand');
				return;
			} else {
				this.element.removeClass('invalid-hand');
			}
			this.updateShanten(handCalculator);
			this.updatePoints(handCalculator);
			this.updateWinningTiles(handCalculator);
			this.updatePayment(handCalculator);
			this.updateFu(handCalculator);
		};
		InfoArea.prototype.updateShanten = function(handCalculator){
			var shanten = handCalculator.getShanten();
			if(handCalculator.getBestPoints().han > 0){
				this.shanten.text('Win');
			} else if(shanten == 0){
				this.shanten.text("Tempai");
			} else {
				this.shanten.text(shanten);
			}
		};
		InfoArea.prototype.updatePoints = function(handCalculator){
			var points = handCalculator.getBestPoints();
			this.pointBreakdown.empty();
			if(points){
				for(var key in points.breakdown){
					if(!points.breakdown.hasOwnProperty(key)){
						continue;
					}
					this.pointBreakdown.append($('<tr><td>' + __(key) + '</td><td>' + points.breakdown[key] + '</td></tr>'));
				}
				this.pointBreakdown.append($('<tr><td>' + __('Total') + '</td><td>' + points.han + '</td></tr>'));
			}
		};
		InfoArea.prototype.updateFu = function(handCalculator){
			var points = handCalculator.getBestPoints();
			this.fuBreakdown.empty();
			if(points){
				for(var key in points.fuBreakdown){
					if(!points.fuBreakdown.hasOwnProperty(key)){
						continue;
					}
					if(points.fuBreakdown[key] > 0){
						this.fuBreakdown.append($('<tr><td>' + __(key) + '</td><td>' + points.fuBreakdown[key] + '</td></tr>'));
					}
				}
			}
		};
		InfoArea.prototype.updateWinningTiles = function(handCalculator){
			this.waits.empty();
			if(handCalculator.getShanten() == 0){
				// This hand is in temapi
				var waits = handCalculator.getWaits();
				for (var i = 0; i < waits.length; i++) {
					var wait = waits[i].toString();
					var img = ImageManager.getTileImage(wait);
					this.waits.append(img);
				};
			}
		};
		InfoArea.prototype.updatePayment = function(handCalculator){
			var points = handCalculator.getBestPoints();
			if(!points){
				return;
			}
			if(points.dealerPays){
				this.payment.text(points.othersPay + "/" + points.dealerPays);
				this.paymentNoLimits.text(points.noLimitsOthersPay + "/" + points.noLimitsDealerPays);
			} else if(points.isDealer && points.tsumo) {
				this.payment.text(points.othersPay + " all");
				this.paymentNoLimits.text(points.noLimitsOthersPay + " all");
			} else {
				this.payment.text(points.othersPay);
				this.paymentNoLimits.text(points.noLimitsOthersPay);
			}
		};
		InfoArea.prototype.getElement = function(){
			return this.element;
		}
		return InfoArea;
	}());

	var Calculator = (function(){
		var OptionsArea = $(
			'<div class="row-fluid span12 options-area">' +
				'<div class="span3"><h3>Common Points</h3>' +
					'<label class="checkbox"><input type="checkbox" id="option-riichi">Riichi</label>' +
					'<label class="checkbox"><input type="checkbox" id="option-ippatsu">Ippatsu</label>' +
					'<label class="checkbox"><input type="checkbox" id="option-tsumo">Tsumo</label>' +
					'<input class="btn reset-button" type="button" value="Reset">' +
				'</div>' +
				'<div class="span3"><h3>Round Wind</h3>' +
					'<label class="radio"><input type="radio" name="round-wind" value="1" checked>East</label>' +
					'<label class="radio"><input type="radio" name="round-wind" value="2">South</label>' +
					'<label class="radio"><input type="radio" name="round-wind" value="3">West</label>' +
					'<label class="radio"><input type="radio" name="round-wind" value="4">North</label>' +
				'</div>' +
				'<div class="span3"><h3>Seat Wind</h3>' +
					'<label class="radio"><input type="radio" name="seat-wind" value="1" checked>East</label>' +
					'<label class="radio"><input type="radio" name="seat-wind" value="2">South</label>' +
					'<label class="radio"><input type="radio" name="seat-wind" value="3">West</label>' +
					'<label class="radio"><input type="radio" name="seat-wind" value="4">North</label>' +
				'</div>' +
				'<div class="span3"><h3>Uncommon Points</h3>' +
					'<label class="checkbox"><input type="checkbox" id="option-haitei-houtei">Hai Tei/Hou Tei</label>' +
					'<label class="checkbox"><input type="checkbox" id="option-double-riichi">Double Riichi</label>' +
					'<label class="checkbox"><input type="checkbox" id="option-chan-kan">Chan Kan</label>' +
					'<label class="checkbox"><input type="checkbox" id="option-rinshan-kaihou">Rinshan Kaihou</label>' +
					'<label class="checkbox"><input type="checkbox" id="option-open-riichi">Open Riichi</label>' +
				'</div>' +
			'<div>'
		).appendTo(ContainerElement);
		var GridArea = $(
			'<div class="row-fluid">' +
			'<div class="span9 left-panel">' + 
				'<div class="row-fluid">' +
					'<div class="span8 first-tile-row"><h3>Hand</h3></div>' + 
					'<div class="span4 second-tile-row"><h3>Winning Tile</h3></div>' + 
				'</div><div class="row-fluid">' +
					'<div class="span12 third-tile-row"><h3>Calls</h3></div>' +
				'</div><div class="row-fluid">' +
					'<div class="span12 forth-tile-row"><h3>Dora Indicators</h3></div>' +
				'</div><div class="row-fluid">' +
					'<div class="span12 fifth-tile-row"><h3>Closed Kans</h3></div>' +
				'</div>' +
			'</div>' +
			'<div class="span3 right-panel"></div>' + 
			'</div>'
		).appendTo(ContainerElement);

		var RightPanel = GridArea.find('.right-panel').first();
		var RightInfoArea = new InfoArea();
		RightInfoArea.getElement().appendTo(RightPanel);

		var HandArea = new TileArea();
		HandArea.getElement().appendTo(GridArea.find('.first-tile-row').first());

		var WinArea = new TileArea();
		WinArea.getElement().appendTo(GridArea.find('.second-tile-row').first());

		var MeldsArea = new TileArea();
		MeldsArea.getElement().appendTo(GridArea.find('.third-tile-row').first());

		var DoraArea = new TileArea();
		DoraArea.getElement().appendTo(GridArea.find('.forth-tile-row').first());

		var KanArea = new TileArea();
		KanArea.getElement().appendTo(GridArea.find('.fifth-tile-row').first());

		$('.reset-button', OptionsArea).click(function(){
			TileInput.close();
			HandArea.reset();
			WinArea.reset();
			MeldsArea.reset();
			DoraArea.reset();
			KanArea.reset();
			updateAll();
		});
		var calculateDora = function(){
			var doraIndicators = DoraArea.getTiles();
			var doraCount = 0;

			var normalizedTiles = [].concat(HandArea.getNormalizedTiles(),
				WinArea.getNormalizedTiles(),
				MeldsArea.getNormalizedTiles(),
				KanArea.getNormalizedTiles());

			var allTiles = [].concat(HandArea.getTiles(),
				WinArea.getTiles(),
				MeldsArea.getTiles(),
				KanArea.getTiles());

			for (var i = 0; i < doraIndicators.length; i++) {
				var indicator = Riichi.Tile.getDoraFromIndicator(doraIndicators[i]);
				for (var j = 0; j < normalizedTiles.length; j++) {
					if(normalizedTiles[j] == indicator){
						doraCount++;
					}
				};
			};

			for (var i = 0; i < allTiles.length; i++) {
				if(allTiles[i][0] == "0"){
					doraCount++;
				}
			};
			return doraCount;
		};
		var updateAll = function(){
			// Get the hand
			var handTiles = HandArea.getNormalizedTiles();
			var meldTiles = MeldsArea.getNormalizedTiles();
			if(handTiles.length == 0){
				return;
			}
			var stringTiles = handTiles.join("");
			
			var calculator = new Riichi.Hand({text:stringTiles});
			if(WinArea.getTiles().length > 0){
				// Get the winning tile
				var stringWinningTile = WinArea.getNormalizedTiles()[0];
				var winningTile = new Riichi.Tile({text:stringWinningTile});
				// Calculate doras
				var doraCount = calculateDora();
				// Build the calculator and set all the options		
				// Do open melds
				var melds = MeldsArea.parseAsMelds();
				if(melds !== null){
					for (var i = 0; i < melds.length; i++) {
						calculator.addOpenMeld(melds[i]);
					};
				}
				// Now closed kans
				var kans = KanArea.getAsKans();
				if(kans !== null){
					for (var i = 0; i < kans.length; i++) {
						console.log(kans);
						calculator.addClosedKan(kans[i]);
					};
				}
				calculator.setWinningTile(winningTile);
				calculator.setOptions({
					// Points
					riichi: $('#option-riichi').prop('checked'),
					doubleRiichi: $('#option-double-riichi').prop('checked'),
					chanKan: $('#option-chan-kan').prop('checked'),
					haiTei: $('#option-haitei-houtei').prop('checked') && $('#option-tsumo').prop('checked'),
					houTei: $('#option-haitei-houtei').prop('checked') && !$('#option-tsumo').prop('checked'),
					ippatsu: $('#option-ippatsu').prop('checked'),
					rinshanKaihou: $('#option-rinshan-kaihou').prop('checked'),
					openRiichi: $('#option-open-riichi').prop('checked'),
					tsumo: $('#option-tsumo').prop('checked'),

					dora: doraCount,

					seatWind: parseInt($('input[name=seat-wind]:checked').val()),
					roundWind: parseInt($('input[name=round-wind]:checked').val())
				});
				console.log(calculator.getBestPoints());
			}
			RightInfoArea.update(calculator);
		};

		OptionsArea.find('input').on('change', updateAll);

		return {
			updateAll: updateAll
		}
	}());
};

$(function(){
	new HandCalculator($('.hand-calculator'));
});