package me.TechsCode.ReleaseServer.objects;

public class Deployment {

    private String name;
    private boolean enabled;
    private Remote remote;
    private String path;
    private String[] commands;

    public Deployment(String name, boolean enabled, Remote remote, String path, String[] commands) {
        this.name = name;
        this.enabled = enabled;
        this.remote = remote;
        this.path = path;
        this.commands = commands;
    }

    public String getName() {
        return name;
    }

    public boolean isEnabled() {
        return enabled;
    }

    public Remote getRemote() {
        return remote;
    }

    public String getPath() {
        return path;
    }

    public String[] getCommands() {
        return commands;
    }
}
