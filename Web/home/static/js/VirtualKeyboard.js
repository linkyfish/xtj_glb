function VirtualKeyboard(_oInput, _bPassword, _bRandom, _nPositionX, _nPositionY, _nPositionRandomRange)
{
	win	= open("/include/VirtualKeyboard/VirtualKeyboard.htm?ipt="+ _oInput.id +"&pwm="+ _bPassword +"&rdm="+ _bRandom +"&px="+ _nPositionX +"&py="+ _nPositionY +"&prr="+ _nPositionRandomRange, "VirtualKeyboard", "toolbar=0,scrollbars=0,width=100,height=100") ;
	win.focus() ;
}