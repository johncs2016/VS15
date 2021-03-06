function ComplexNumber(real, imaginary) {
	this.real = real;
	this.imaginary = imaginary;
}

ComplexNumber.prototype = {
	real:0,
	imaginary:0,

	add: function() {
		if(arguments.length == 1) {
			return new ComplexNumber(this.real + arguments[0].real,
				this.imaginary + arguments[0].imaginary);
		} else {
			return new ComplexNumber(this.real + arguments[0],
				this.imaginary + arguments[1]);
		}
	},

	subtract: function() {
		if(arguments.length == 1) {
			return new ComplexNumber(this.real - arguments[0].real,
				this.imaginary - arguments[0].imaginary);
		} else {
			return new ComplexNumber(this.real - arguments[0],
				this.imaginary - arguments[1]);
		}
	},

	multiply: function() {
		var multiplier = arguments[0];
		if(arguments.length != 1) {
			multiplier = new ComplexNumber(arguments[0], arguments[1]);
		}
		var real = this.real * multiplier.real - this.imaginary * multiplier.imaginary;
		var imaginary = this.real * multiplier.imaginary + this.imaginary * multiplier.real;
		return new ComplexNumber(real, imaginary);
	},

	divide: function() {
		var denominator = arguments[0];
		if(arguments.length != 1) {
			denominator = new ComplexNumber(arguments[0], arguments[1]);
		}
		var num = [
			this.real * denominator.real + this.imaginary * denominator.imaginary,
			this.imaginary * denominator.real - this.real * denominator.imaginary
		];
		var denom = Math.pow(denominator.real, 2) + Math.pow(denominator.imaginary, 2);
		var real = num[0] / denom;
		var imaginary = num[1] / denom;
		return new ComplexNumber(real, imaginary); 
	},

	modulus: function() {
		return Math.sqrt(Math.pow(this.real, 2) + Math.pow(this.imaginary, 2));
	},

	conjugate: function() {
		return new ComplexNumber(this.real, -1 * this.imaginary);
	},

	toString: function() {
		if(this.imaginary == 0) {
			return this.real;
		} else {
			var str = "";
			if(this.real == 0) {
				if (this.imaginary == 1) {
					str = "i";
				} else if (this.imaginary == -1) {
					str = "-i";
				} else {
					str = this.imaginary + "i";
				} // end if this.imaginary == 1
			} else {
				str = this.real;
				if(this.imaginary > 0) {
					str += " + ";
				} else {
					str += " - ";
				}  // end if this.imaginary > 0
				if(Math.abs(this.imaginary) == 1) {
					str += "i";
				} else {
					str += Math.abs(this.imaginary) + "i";
				}
			}  // end if this.real == 0
			return str;
		}  // end if this.imaginary == 0
	} // end function
}; // end of prototype

