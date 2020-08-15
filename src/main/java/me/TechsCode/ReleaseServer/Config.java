package me.TechsCode.ReleaseServer;

import com.google.gson.Gson;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;
import me.TechsCode.ReleaseServer.objects.Deployment;
import me.TechsCode.ReleaseServer.objects.Project;
import me.TechsCode.ReleaseServer.objects.Remote;
import org.apache.commons.io.FileUtils;

import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.nio.file.StandardCopyOption;
import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;

public class Config {

    private static final Gson gson = new Gson();

    private File file;
    private JsonObject root;

    public Config() {
        this.file = new File("config.json");

        if(!file.exists()){
            try {
                InputStream src = Config.class.getResourceAsStream("/config.json");
                Files.copy(src, Paths.get(file.toURI()), StandardCopyOption.REPLACE_EXISTING);
            } catch (IOException e) {
                e.printStackTrace();
            }
        }

        try {
            String json = FileUtils.readFileToString(file, StandardCharsets.UTF_8);

            JsonParser jsonParser = new JsonParser();
            root = (JsonObject) jsonParser.parse(json);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public int getPort(){
        return root.get("port").getAsInt();
    }

    private Map<String, Remote> getRemotes(){
        JsonObject jsonObject = root.getAsJsonObject("remotes");

        return jsonObject.entrySet().stream()
                .collect(Collectors.toMap(Map.Entry::getKey, pair -> {
                    JsonObject data = (JsonObject) pair.getValue();
                    String hostname = data.get("hostname").getAsString();
                    int port = data.get("port").getAsInt();
                    String username = data.get("username").getAsString();
                    String password = data.get("password").getAsString();

                    return new Remote(hostname, port, username, password);
                }));
    }

    private List<Deployment> getDeploymentsList(JsonObject jsonObject, Map<String, Remote> remoteMap){
        return jsonObject.entrySet().stream()
                .map(deployment -> {
                    JsonObject data_ = (JsonObject) deployment.getValue();
                    String name_ = deployment.getKey();
                    boolean enabled_ = data_.get("enabled").getAsBoolean();
                    Remote remote_ = remoteMap.getOrDefault(data_.get("remote").getAsString(), null);
                    String path_ = data_.get("path").getAsString();
                    String[] commands = gson.fromJson(data_.getAsJsonArray("commands"), String[].class);

                    return new Deployment(name_, enabled_, remote_, path_, commands);
                }).collect(Collectors.toList());
    }

    public List<Project> getProjects(){
        Map<String, Remote> remoteMap = getRemotes();

        JsonObject jsonObject = root.getAsJsonObject("projects");

        return jsonObject.entrySet().stream()
                .map(project -> {
                    JsonObject data = (JsonObject) project.getValue();
                    String name = project.getKey();
                    String githubRepository = data.get("githubRepository").getAsString();
                    String githubToken = data.get("githubToken").isJsonNull() ? null : data.get("githubToken").getAsString();

                    List<Deployment> deployments = getDeploymentsList(data.getAsJsonObject("deployments"), remoteMap);

                    return new Project(name, githubRepository, githubToken, deployments);
                }).collect(Collectors.toList());
    }
}
