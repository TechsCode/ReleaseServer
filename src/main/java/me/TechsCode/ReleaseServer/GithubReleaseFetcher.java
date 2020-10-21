package me.TechsCode.ReleaseServer;

import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;
import me.TechsCode.ReleaseServer.objects.Project;
import org.apache.commons.io.IOUtils;

import java.io.IOException;
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
                    String randomParameter = "?rnd="+random.nextInt();
                    String accessTokenParameter = project.getGithubToken().isPresent() ? "&access_token="+project.getGithubToken().get() : "";
                    String url = "https://api.github.com/repos/"+project.getGithubRepository()+"/releases"+randomParameter+accessTokenParameter;

                    JsonArray jsonArray = (JsonArray) JsonParser.parseString(IOUtils.toString(new URL(url), StandardCharsets.UTF_8));

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
