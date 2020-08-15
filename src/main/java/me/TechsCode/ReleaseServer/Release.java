package me.TechsCode.ReleaseServer;

import me.TechsCode.ReleaseServer.objects.Project;

import java.util.List;

public class Release {

    private Project project;
    private int id;
    private String name;
    private String uniqueTag;
    private String description;
    private List<Asset> assets;

    public Release(Project project, int id, String name, String uniqueTag, String description, List<Asset> assets) {
        this.project = project;
        this.id = id;
        this.name = name;
        this.uniqueTag = uniqueTag;
        this.description = description;
        this.assets = assets;
    }

    public Project getProject() {
        return project;
    }

    public int getId() {
        return id;
    }

    public String getName() {
        return name;
    }

    public String getUniqueTag() {
        return uniqueTag;
    }

    public String getDescription() {
        return description;
    }

    public List<Asset> getAssets() {
        return assets;
    }
}
