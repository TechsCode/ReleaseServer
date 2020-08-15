package me.TechsCode.ReleaseServer;

public class Asset {

    private int id;
    private String name;
    private String url;

    public Asset(int id, String name, String url) {
        this.id = id;
        this.name = name;
        this.url = url;
    }

    public int getId() {
        return id;
    }

    public String getName() {
        return name;
    }

    public String getUrl() {
        return url;
    }
}
