package me.TechsCode.ReleaseServer;

import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;
import me.TechsCode.ReleaseServer.objects.Project;
import org.apache.commons.io.IOUtils;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.nio.charset.StandardCharsets;
import java.util.ArrayList;
import java.util.List;
import java.util.Random;

public abstract class GithubReleaseFetcher extends Thread {

    private static final int FETCH_DELAY = 1000 * 60 * 3;

    private static final Random random = new Random();

    private final List<Project> projects;

    public GithubReleaseFetcher(List<Project> projects) {
        this.projects = projects;

        start();
    }

    @Override
    public void run() {
        while (true){
            List<Release> releases = new ArrayList<>();

            for(Project project : projects){
                try {
                    String accessTokenParameter = project.getGithubToken().isPresent() ? project.getGithubToken().get() : "";
                    String urlString = "https://api.github.com/repos/"+project.getGithubRepository()+"/releases";

                    URL url = new URL(urlString);
                    HttpURLConnection con = (HttpURLConnection) url.openConnection();
                    con.setRequestMethod("GET");
                    con.setRequestProperty("Authorization", "token "+accessTokenParameter);
                    con.setRequestProperty("Accept", "application/json");

                    BufferedReader in = new BufferedReader(
                            new InputStreamReader(con.getInputStream()));
                    String inputLine;
                    StringBuilder content = new StringBuilder();
                    while ((inputLine = in.readLine()) != null) {
                        content.append(inputLine);
                    }
                    in.close();
                    con.disconnect();

                    JsonArray jsonArray = JsonParser.parseString(content.toString()).getAsJsonArray();

                    for(JsonElement jsonElement : jsonArray){
                        JsonObject release = (JsonObject) jsonElement;

                        int id = release.get("id").getAsInt();
                        String name = release.get("name").getAsString();
                        String uniqueTag = release.get("tag_name").getAsString();
                        String description = release.get("body").getAsString();

                        List<Asset> assets = new ArrayList<>();
                        for(JsonElement all : release.get("assets").getAsJsonArray()){
                            JsonObject asset = (JsonObject) all;

                            int id_ = asset.get("id").getAsInt();
                            String name_ = asset.get("name").getAsString();
                            String url_ = asset.get("url").getAsString();

                            assets.add(new Asset(id_, name_, url_));
                        }

                        releases.add(new Release(project, id, name, uniqueTag, description, assets));
                    }
                } catch (IOException e) {
                    e.printStackTrace();
                    System.out.println("Could not fetch releases for "+project.getName()+" ("+project.getGithubRepository()+")");
                }
            }

            onRetrieve(releases);

            try {
                sleep(FETCH_DELAY);
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
        }
    }

    public abstract void onRetrieve(List<Release> releases);
}
