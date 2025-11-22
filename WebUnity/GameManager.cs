
using UnityEngine;
using UnityEngine.SceneManagement;

public class GameManager : MonoBehaviour
{
    [Header("Game Info")]
    public string currentGameId = "";
    public string walletAddress = "";
    
    [Header("Debug")]
    public bool showDebugInfo = true;
    
    void Start()
    {
        Debug.Log("🎮 GameManager Started");
        
        // Coba baca dari localStorage jika ada
        TryLoadFromLocalStorage();
    }
    
    // Dipanggil dari JavaScript bridge.js
    public void OnWalletConnected(string address) 
    {
        walletAddress = address;
        Debug.Log("👛 Wallet Connected: " + address);
        
        if (showDebugInfo)
        {
            ShowDebugMessage("Wallet Connected: " + ShortenAddress(address));
        }
        
        // Save to PlayerPrefs untuk persistent
        PlayerPrefs.SetString("WalletAddress", address);
        PlayerPrefs.Save();
    }
    
    // Dipanggil dari JavaScript untuk set game ID
    public void SetGameId(string gameId) 
    {
        currentGameId = gameId;
        Debug.Log("🎮 Game ID Set: " + gameId);
        
        if (showDebugInfo)
        {
            ShowDebugMessage("Loading Game: " + gameId);
        }
        
        // Load scene atau level berdasarkan gameId
        LoadGameBasedOnId(gameId);
    }
    
    // Load game/level berdasarkan ID
    void LoadGameBasedOnId(string gameId)
    {
        Debug.Log("📂 Loading game: " + gameId);
        
        switch(gameId.ToLower())
        {
            case "fly-to-moon":
            case "fly-to-the-moon":
                LoadScene("MoonLevel"); // Nama scene di Unity
                break;
                
            case "block-puzzle":
                LoadScene("PuzzleLevel");
                break;
                
            case "racing-hero":
                LoadScene("RacingLevel");
                break;
                
            default:
                Debug.LogWarning("⚠️ Unknown game ID: " + gameId);
                // Load default scene atau main menu
                LoadScene("MainGame");
                break;
        }
    }
    
    void LoadScene(string sceneName)
    {
        // Check jika scene ada di build settings
        if (Application.CanStreamedLevelBeLoaded(sceneName))
        {
            Debug.Log("✅ Loading scene: " + sceneName);
            SceneManager.LoadScene(sceneName);
        }
        else
        {
            Debug.LogError("❌ Scene not found: " + sceneName);
            Debug.LogWarning("💡 Make sure scene is added to Build Settings");
        }
    }
    
    // Helper: Baca dari localStorage browser
    void TryLoadFromLocalStorage()
    {
        #if UNITY_WEBGL && !UNITY_EDITOR
        try 
        {
            string wallet = GetLocalStorageItem("kulino_wallet_address");
            string game = GetLocalStorageItem("kulino_current_game");
            
            if (!string.IsNullOrEmpty(wallet))
            {
                OnWalletConnected(wallet);
            }
            
            if (!string.IsNullOrEmpty(game))
            {
                SetGameId(game);
            }
        }
        catch (System.Exception e)
        {
            Debug.LogWarning("⚠️ Failed to load from localStorage: " + e.Message);
        }
        #endif
    }
    
    // Get item from browser localStorage
    string GetLocalStorageItem(string key)
    {
        #if UNITY_WEBGL && !UNITY_EDITOR
        return Application.ExternalEval($"localStorage.getItem('{key}')");
        #else
        return "";
        #endif
    }
    
    // Helper functions
    string ShortenAddress(string addr)
    {
        if (string.IsNullOrEmpty(addr) || addr.Length < 10) return addr;
        return addr.Substring(0, 6) + "..." + addr.Substring(addr.Length - 4);
    }
    
    void ShowDebugMessage(string message)
    {
        // Tampilkan di UI atau console
        Debug.Log("📢 " + message);
        
        // Bisa tambahkan UI Text untuk debug
        // debugText.text = message;
    }
    
    // Public getters
    public string GetWalletAddress() 
    {
        return walletAddress;
    }
    
    public string GetCurrentGameId()
    {
        return currentGameId;
    }

using UnityEngine;
using UnityEngine.SceneManagement;

public class GameManager : MonoBehaviour
{
    [Header("Game Info")]
    public string currentGameId = "";
    public string walletAddress = "";
    
    [Header("Debug")]
    public bool showDebugInfo = true;
    
    void Start()
    {
        Debug.Log("🎮 GameManager Started");
        
        // Coba baca dari localStorage jika ada
        TryLoadFromLocalStorage();
    }
    
    // Dipanggil dari JavaScript bridge.js
    public void OnWalletConnected(string address) 
    {
        walletAddress = address;
        Debug.Log("👛 Wallet Connected: " + address);
        
        if (showDebugInfo)
        {
            ShowDebugMessage("Wallet Connected: " + ShortenAddress(address));
        }
        
        // Save to PlayerPrefs untuk persistent
        PlayerPrefs.SetString("WalletAddress", address);
        PlayerPrefs.Save();
    }
    
    // Dipanggil dari JavaScript untuk set game ID
    public void SetGameId(string gameId) 
    {
        currentGameId = gameId;
        Debug.Log("🎮 Game ID Set: " + gameId);
        
        if (showDebugInfo)
        {
            ShowDebugMessage("Loading Game: " + gameId);
        }
        
        // Load scene atau level berdasarkan gameId
        LoadGameBasedOnId(gameId);
    }
    
    // Load game/level berdasarkan ID
    void LoadGameBasedOnId(string gameId)
    {
        Debug.Log("📂 Loading game: " + gameId);
        
        switch(gameId.ToLower())
        {
            case "fly-to-moon":
            case "fly-to-the-moon":
                LoadScene("MoonLevel"); // Nama scene di Unity
                break;
                
            case "block-puzzle":
                LoadScene("PuzzleLevel");
                break;
                
            case "racing-hero":
                LoadScene("RacingLevel");
                break;
                
            default:
                Debug.LogWarning("⚠️ Unknown game ID: " + gameId);
                // Load default scene atau main menu
                LoadScene("MainGame");
                break;
        }
    }
    
    void LoadScene(string sceneName)
    {
        // Check jika scene ada di build settings
        if (Application.CanStreamedLevelBeLoaded(sceneName))
        {
            Debug.Log("✅ Loading scene: " + sceneName);
            SceneManager.LoadScene(sceneName);
        }
        else
        {
            Debug.LogError("❌ Scene not found: " + sceneName);
            Debug.LogWarning("💡 Make sure scene is added to Build Settings");
        }
    }
    
    // Helper: Baca dari localStorage browser
    void TryLoadFromLocalStorage()
    {
        #if UNITY_WEBGL && !UNITY_EDITOR
        try 
        {
            string wallet = GetLocalStorageItem("kulino_wallet_address");
            string game = GetLocalStorageItem("kulino_current_game");
            
            if (!string.IsNullOrEmpty(wallet))
            {
                OnWalletConnected(wallet);
            }
            
            if (!string.IsNullOrEmpty(game))
            {
                SetGameId(game);
            }
        }
        catch (System.Exception e)
        {
            Debug.LogWarning("⚠️ Failed to load from localStorage: " + e.Message);
        }
        #endif
    }
    
    // Get item from browser localStorage
    string GetLocalStorageItem(string key)
    {
        #if UNITY_WEBGL && !UNITY_EDITOR
        return Application.ExternalEval($"localStorage.getItem('{key}')");
        #else
        return "";
        #endif
    }
    
    // Helper functions
    string ShortenAddress(string addr)
    {
        if (string.IsNullOrEmpty(addr) || addr.Length < 10) return addr;
        return addr.Substring(0, 6) + "..." + addr.Substring(addr.Length - 4);
    }
    
    void ShowDebugMessage(string message)
    {
        // Tampilkan di UI atau console
        Debug.Log("📢 " + message);
        
        // Bisa tambahkan UI Text untuk debug
        // debugText.text = message;
    }
    
    // Public getters
    public string GetWalletAddress() 
    {
        return walletAddress;
    }
    
    public string GetCurrentGameId()
    {
        return currentGameId;
    }

}